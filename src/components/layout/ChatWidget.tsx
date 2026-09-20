// ویجت چت آنلاین
import { useEffect, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import Icon from '../shared/Icon';
import { useChat } from '../../store';
import { faDateTime, faNum } from '../../lib/format';

export default function ChatWidget() {
  const { open, messages, typing, send, markSeen, toggle, unread } = useChat();
  const [text, setText] = useState('');
  const bodyRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (open) { markSeen(); bodyRef.current?.scrollTo({ top: bodyRef.current.scrollHeight, behavior: 'smooth' }); }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, messages.length, typing]);

  return (
    <>
      <button className="chat-fab" onClick={() => { toggle(); markSeen(); }} aria-label="چت آنلاین">
        <Icon name="chat" size={22} />
        {!open && unread > 0 && <span className="dot-gold">{faNum(unread)}</span>}
      </button>
      <AnimatePresence>
      {open && (
        <motion.div className="chat-window" initial={{ opacity: 0, y: 20, scale: .96 }} animate={{ opacity: 1, y: 0, scale: 1 }} exit={{ opacity: 0, y: 20, scale: .96 }}>
          <div className="chat-head">
            <div className="flex items-center gap-2">
              <span className="pulse-dot" />
              <div>
                <b style={{ fontSize: 13.5 }}>پشتیبانی آنلاین هوش‌یار</b>
                <div className="small muted">معمولاً در چند دقیقه پاسخ می‌دهیم</div>
              </div>
            </div>
            <span className="badge badge-teal small">آنلاین</span>
          </div>
          <div className="chat-body" ref={bodyRef}>
            {messages.map((m) => (
              <div key={m.id} className={`chat-msg ${m.from === 'me' ? 'me' : 'them'}`}>
                <div className="chat-bubble">
                  <span className="chat-name small">{m.name}</span>
                  <p>{m.text}</p>
                  <span className="chat-time">{faDateTime(m.date).split('—')[1]} {m.from === 'me' && (m.status === 'sent' ? '✓' : '✓✓')}</span>
                </div>
              </div>
            ))}
            {typing && (
              <div className="chat-msg them">
                <div className="chat-bubble typing"><span /><span /><span /></div>
              </div>
            )}
          </div>
          <form className="chat-composer" onSubmit={(e) => { e.preventDefault(); if (text.trim()) { send(text.trim()); setText(''); } }}>
            <input className="input" placeholder="پیام خود را بنویسید..." value={text} onChange={(e) => setText(e.target.value)} style={{ height: 44, borderRadius: 12 }} />
            <button type="submit" className="chat-send" aria-label="ارسال"><Icon name="send-h" size={18} /></button>
          </form>
          <div className="chat-note small muted">تعداد پیام‌ها: {faNum(messages.length)} — نسخه نمایشی؛ با اتصال وردپرس، پشتیبان واقعی پاسخ می‌دهد.</div>
        </motion.div>
      )}
      </AnimatePresence>
    </>
  );
}
