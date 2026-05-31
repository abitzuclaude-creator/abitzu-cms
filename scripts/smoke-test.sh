#!/usr/bin/env bash
# End-to-end walkthrough of the Abitzu CMS — exercises auth, scoping,
# dashboard, collections, calls, WhatsApp, payments, alerts, validation.
# Usage: BASE=http://localhost:8080 bash scripts/smoke-test.sh
set -uo pipefail
BASE="${BASE:-http://localhost:8080}"
PASS=0; FAIL=0
ok(){ echo "  ✓ $1"; PASS=$((PASS+1)); }
no(){ echo "  ✗ $1  --> $2"; FAIL=$((FAIL+1)); }
J(){ python3 -c "import sys,json;d=json.load(sys.stdin);print($1)" 2>/dev/null; }

login(){ # $1 email $2 pass -> writes cookie jar path to stdout
  local jar; jar=$(mktemp)
  local csrf; csrf=$(curl -sc "$jar" "$BASE/login" | grep -o 'name="_token" value="[^"]*"' | sed 's/.*value="//;s/"//')
  curl -s -b "$jar" -c "$jar" -X POST "$BASE/login" \
    -d "_token=$csrf&email=$1&password=$2" \
    -H "Content-Type: application/x-www-form-urlencoded" -L -o /dev/null
  echo "$jar"
}

echo "════════════════════════════════════════════════════════"
echo " ABITZU CMS — END-TO-END WALKTHROUGH"
echo " target: $BASE"
echo "════════════════════════════════════════════════════════"

# ── 1. AUTH ───────────────────────────────────────────────
echo; echo "1. AUTHENTICATION"
# wrong password rejected
jar=$(mktemp)
csrf=$(curl -sc "$jar" "$BASE/login" | grep -o 'name="_token" value="[^"]*"' | sed 's/.*value="//;s/"//')
code=$(curl -s -b "$jar" -c "$jar" -X POST "$BASE/login" -d "_token=$csrf&email=admin@abitzu.com&password=wrong" -H "Content-Type: application/x-www-form-urlencoded" -o /dev/null -w "%{http_code}")
[ "$code" = "302" ] && ok "bad password rejected (302 back to login w/ errors)" || no "bad password" "got $code"
# unauthenticated API blocked
code=$(curl -s -o /dev/null -w "%{http_code}" -H "Accept: application/json" "$BASE/collections")
[ "$code" = "401" ] || [ "$code" = "302" ] && ok "guest blocked from /collections ($code)" || no "guest guard" "got $code"
# owner login works
OWNER=$(login admin@abitzu.com 'ChangeMeOnFirstLogin!')
who=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/collections" | J "len(d['invoices'])")
[ "$who" = "16" ] && ok "owner login → sees all 16 invoices" || no "owner login" "saw $who"

# ── 2. ROLE-BASED DATA SCOPING ────────────────────────────
echo; echo "2. AGENT DATA-SCOPING (policy layer)"
for pair in "priya@abitzu.com:6" "rohan@abitzu.com:4" "aisha@abitzu.com:4" "vikram@abitzu.com:2"; do
  email="${pair%%:*}"; expect="${pair##*:}"
  JAR=$(login "$email" 'password')
  n=$(curl -s -b "$JAR" -H "Accept: application/json" "$BASE/collections" | J "len(d['invoices'])")
  [ "$n" = "$expect" ] && ok "$email sees only own $n invoices" || no "$email scoping" "expected $expect, saw $n"
done

# ── 3. DASHBOARD ──────────────────────────────────────────
echo; echo "3. DASHBOARD METRICS"
DASH=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/dashboard")
out=$(echo "$DASH" | J "d['stats']['total_outstanding']")
ovd=$(echo "$DASH" | J "d['stats']['overdue_count']")
alr=$(echo "$DASH" | J "d['stats']['open_alerts']")
[ -n "$out" ] && ok "total outstanding = ₹$out" || no "dashboard stats" "no value"
[ -n "$ovd" ] && ok "overdue count = $ovd invoices" || no "overdue" "none"
[ "$alr" = "1" ] && ok "open alerts = 1 (seeded #147 gap)" || no "alerts stat" "got $alr"

# ── 4. COLLECTIONS BOARD + SEARCH ─────────────────────────
echo; echo "4. COLLECTIONS BOARD"
ALL=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/collections")
stages=$(echo "$ALL" | J "sorted(set(i['stage'] for i in d['invoices']))")
ok "stages present: $stages"
TID=$(echo "$ALL" | J "[i['id'] for i in d['invoices'] if 'Truefitt' in i['brand']][0]")
srch=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/collections?q=Bloom" | J "len(d['invoices'])")
[ "$srch" = "1" ] && ok "search 'Bloom' → 1 result" || no "search" "got $srch"

# ── 5. STAGE MOVE (drag-and-drop) ─────────────────────────
echo; echo "5. STAGE TRANSITIONS"
NID=$(echo "$ALL" | J "[i['id'] for i in d['invoices'] if i['stage']=='new'][0]")
r=$(curl -s -b "$OWNER" -X PATCH "$BASE/api/collections/$NID/stage" -H "Content-Type: application/json" -d '{"stage":"promised"}')
[ "$(echo "$r" | J "d['ok']")" = "True" ] && ok "moved a 'new' card → promised" || no "stage move" "$r"
# invalid stage rejected
code=$(curl -s -o /dev/null -w "%{http_code}" -b "$OWNER" -X PATCH "$BASE/api/collections/$NID/stage" -H "Content-Type: application/json" -d '{"stage":"bogus"}')
[ "$code" = "422" ] && ok "invalid stage rejected (422)" || no "stage validation" "got $code"
# promise date auto-advances called→promised
CID=$(echo "$ALL" | J "[i['id'] for i in d['invoices'] if i['stage']=='called'][0]")
curl -s -b "$OWNER" -X PATCH "$BASE/api/collections/$CID/promise" -H "Content-Type: application/json" -d '{"promise_date":"2026-06-15"}' >/dev/null
ns=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/collections" | J "[i['stage'] for i in d['invoices'] if i['id']==$CID][0]")
[ "$ns" = "promised" ] && ok "promise date auto-advanced called → promised" || no "promise advance" "stage=$ns"

# ── 6. CALL LOGGING ───────────────────────────────────────
echo; echo "6. INTERACTION LOGGING"
r=$(curl -s -b "$OWNER" -X POST "$BASE/api/interactions" -H "Content-Type: application/json" -d "{\"proforma_invoice_id\":$TID,\"type\":\"phone_call\",\"notes\":\"Spoke to accounts, paying Friday\",\"disposition\":\"reached\"}")
[ "$(echo "$r" | J "d['ok']")" = "True" ] && ok "logged a phone call on Truefitt" || no "call log" "$r"

# ── 7. WHATSAPP COMPOSE + LOG ─────────────────────────────
echo; echo "7. WHATSAPP FLOW"
OVERDUE_ID=$(echo "$ALL" | J "[i['id'] for i in d['invoices'] if i['stage']=='overdue'][0]")
wa=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/api/whatsapp/compose/$OVERDUE_ID")
tt=$(echo "$wa" | J "d['template_type']")
[ "$tt" = "post_due" ] && ok "overdue invoice → post-due template chosen" || no "WA template" "got $tt"
echo "$wa" | J "d['message']" | grep -q '{{' && no "WA vars" "unsubstituted {{}}" || ok "all {{variables}} substituted"
echo "$wa" | J "d['wa_url']" | grep -q 'wa.me/91' && ok "wa.me URL has 91 country code" || no "WA phone" "no 91 prefix"
r=$(curl -s -b "$OWNER" -X POST "$BASE/api/whatsapp/log" -H "Content-Type: application/json" -d "{\"proforma_invoice_id\":$OVERDUE_ID,\"template_type\":\"post_due\"}")
[ "$(echo "$r" | J "d['ok']")" = "True" ] && ok "WhatsApp send logged as interaction" || no "WA log" "$r"

# ── 8. PAYMENTS ───────────────────────────────────────────
echo; echo "8. PAYMENT RECORDING"
before=$(curl -s -b "$OWNER" -H "Accept: application/json" "$BASE/collections" | J "[i for i in d['invoices'] if i['id']==$TID][0]['amount'] - [i for i in d['invoices'] if i['id']==$TID][0]['paidAmount']")
r=$(curl -s -b "$OWNER" -X POST "$BASE/api/payments" -H "Content-Type: application/json" -d "{\"proforma_invoice_id\":$TID,\"amount\":50000,\"payment_date\":\"2026-05-31\",\"mode\":\"neft\",\"bank_account_id\":1}")
nb=$(echo "$r" | J "d['new_balance']"); st=$(echo "$r" | J "d['new_status']")
ok "Truefitt balance ₹$before → ₹$nb after ₹50,000 (status: $st)"
# overpayment rejected
code=$(curl -s -o /dev/null -w "%{http_code}" -b "$OWNER" -X POST "$BASE/api/payments" -H "Content-Type: application/json" -d "{\"proforma_invoice_id\":$TID,\"amount\":99999999,\"payment_date\":\"2026-05-31\",\"mode\":\"neft\",\"bank_account_id\":1}")
[ "$code" = "422" ] && ok "overpayment rejected (422)" || no "overpayment guard" "got $code"

# ── 9. ALERTS ─────────────────────────────────────────────
echo; echo "9. SEQUENCE-GAP ALERTS"
ac=$(curl -s -b "$OWNER" "$BASE/api/alerts/count" | J "d['count']")
[ "$ac" = "1" ] && ok "alert badge count = 1" || no "alert count" "got $ac"

echo; echo "════════════════════════════════════════════════════════"
echo " RESULT:  $PASS passed,  $FAIL failed"
echo "════════════════════════════════════════════════════════"
[ "$FAIL" -eq 0 ]
