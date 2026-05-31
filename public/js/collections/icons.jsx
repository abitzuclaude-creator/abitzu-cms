// ---- Icons + Avatar ---------------------------------------------------
const PATHS = {
  search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
  board: '<rect x="3" y="4" width="5" height="16" rx="1.3"/><rect x="10" y="4" width="5" height="11" rx="1.3"/><rect x="17" y="4" width="4" height="14" rx="1.3"/>',
  table: '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M3.5 9.5h17M3.5 14.5h17M9 9.5v10"/>',
  grid: '<rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/>',
  phone: '<path d="M5 3h3.5l1.5 4.5-2 1.5a12 12 0 0 0 5 5l1.5-2 4.5 1.5V21a1 1 0 0 1-1 1A17 17 0 0 1 4 5a1 1 0 0 1 1-1z"/>',
  arrowRight: '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
  check: '<path d="m4 12 5 5L20 6"/>',
  clock: '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
  rupee: '<path d="M7 4h10M7 8h10M16 4c0 4-3 5-6 5h-1l6 7"/>',
  alert: '<path d="M12 3 2.5 20h19z"/><path d="M12 10v4"/><circle cx="12" cy="17.4" r=".7" fill="currentColor"/>',
  calendar: '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
  trend: '<path d="M3 17 9 11l3 3 7-8"/><path d="M15 6h4v4"/>',
  mail: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 8.5 6 8.5-6"/>',
  filter: '<path d="M3 5h18l-7 8v6l-4-2v-4z"/>',
  x: '<path d="M6 6 18 18M18 6 6 18"/>',
  file: '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
  message: '<path d="M4 5h16v11H9l-4 4z"/>',
  bolt: '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
  download: '<path d="M12 4v11"/><path d="m7.5 11 4.5 4 4.5-4"/><path d="M4 19h16"/>',
  whatsapp: '<path d="M12 3a8.5 8.5 0 0 0-7.3 12.9L3.5 21l5.3-1.4A8.5 8.5 0 1 0 12 3z"/>',
};
function Icon({ name, size = 16, stroke = 1.8, style }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor"
      strokeWidth={stroke} strokeLinecap="round" strokeLinejoin="round" style={style}
      dangerouslySetInnerHTML={{ __html: PATHS[name] || '' }} />
  );
}

function Avatar({ member, size = 24 }) {
  if (!member) return null;
  return (
    <span className="avatar" style={{ width: size, height: size, background: member.color, fontSize: size * 0.4 }}>
      {member.initials}
    </span>
  );
}

Object.assign(window, { Icon, Avatar });
