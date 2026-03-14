import { useQuery } from '@tanstack/react-query';
import { motion } from 'framer-motion';
import { Rss, Terminal, Clock } from 'lucide-react';
import { fetchJobs } from './api/client';
import JobList from './components/JobList';
import ParseButton from './components/ParseButton';

const stagger = {
  hidden: {},
  visible: {
    transition: { staggerChildren: 0.08 },
  },
};

const fadeUp = {
  hidden: { opacity: 0, y: 12 },
  visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease: 'easeOut' } },
};

const ruleReveal = {
  hidden: { scaleX: 0 },
  visible: { scaleX: 1, transition: { duration: 0.8, ease: [0.22, 1, 0.36, 1] } },
};

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function Masthead() {
  return (
    <motion.header
      className="pt-8 pb-4 px-4"
      variants={stagger}
      initial="hidden"
      animate="visible"
    >
      <motion.div className="rule-double" variants={ruleReveal} style={{ originX: 0.5 }} />

      <motion.div className="text-center py-6" variants={fadeUp}>
        <p className="font-mono text-[10px] tracking-[0.4em] uppercase text-gold mb-3">
          Est. 2026 — Automated Intelligence Dispatch
        </p>
        <h1 className="font-playfair text-6xl md:text-8xl font-black tracking-tight leading-none">
          PARSER FACTORY
        </h1>
        <p className="font-baskerville italic text-lg md:text-xl text-ink/60 mt-3">
          "All the Jobs That Are Fit to Parse"
        </p>
      </motion.div>

      <motion.div className="rule-double" variants={ruleReveal} style={{ originX: 0.5 }} />

      <motion.div
        className="flex items-center justify-between py-2 mt-1 font-mono text-[11px] tracking-wide text-ink/50 uppercase"
        variants={fadeUp}
      >
        <span>Vol. I · No. 1</span>
        <span>{formatDate(new Date().toISOString())}</span>
        <ParseButton />
      </motion.div>

      <motion.div className="rule-thin" variants={ruleReveal} style={{ originX: 0.5 }} />
    </motion.header>
  );
}

function StatsBar() {
  const { data } = useQuery({
    queryKey: ['jobs', {}],
    queryFn: () => fetchJobs(),
  });

  const total = data?.total ?? '—';
  const sources = data?.data
    ? new Set(data.data.map((j) => j.source)).size
    : '—';
  const lastUpdated = data?.data?.[0]?.created_at
    ? timeAgo(new Date(data.data[0].created_at))
    : '—';

  return (
    <motion.div
      className="grid grid-cols-3 divide-x divide-rule border-y border-rule my-4"
      variants={stagger}
      initial="hidden"
      animate="visible"
    >
      {[
        { label: 'Listings Parsed', value: String(total), icon: Terminal },
        { label: 'Active Sources', value: String(sources), icon: Rss },
        { label: 'Last Updated', value: lastUpdated, icon: Clock },
      ].map(({ label, value, icon: Icon }) => (
        <motion.div
          key={label}
          className="flex items-center justify-center gap-3 py-3 px-4"
          variants={fadeUp}
        >
          <Icon size={14} className="text-gold" strokeWidth={1.5} />
          <div>
            <p className="font-playfair text-xl font-bold leading-tight">{value}</p>
            <p className="font-mono text-[10px] uppercase tracking-wider text-ink/50">{label}</p>
          </div>
        </motion.div>
      ))}
    </motion.div>
  );
}

function timeAgo(date) {
  const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
  if (seconds < 60) return 'Just Now';
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes}m ago`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours}h ago`;
  const days = Math.floor(hours / 24);
  return `${days}d ago`;
}

function Footer() {
  return (
    <motion.footer
      className="mt-8 px-4 pb-8"
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      transition={{ delay: 0.8, duration: 0.6 }}
    >
      <div className="rule-double" />
      <div className="flex items-center justify-between py-3">
        <p className="font-mono text-[10px] text-ink/40 uppercase tracking-wider">
          Parser Factory © 2026
        </p>
        <div className="flex items-center gap-1 text-ink/30">
          <span className="ornament" />
        </div>
        <p className="font-mono text-[10px] text-ink/40 uppercase tracking-wider">
          Automated · Curated · Parsed
        </p>
      </div>
      <div className="rule-thin" />
    </motion.footer>
  );
}

export default function App() {
  return (
    <div className="max-w-4xl mx-auto min-h-screen">
      <Masthead />
      <StatsBar />
      <JobList />
      <Footer />
    </div>
  );
}
