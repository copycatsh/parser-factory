import { useQuery } from '@tanstack/react-query';
import { motion } from 'framer-motion';
import { ChevronRight } from 'lucide-react';
import { fetchJobs } from '../api/client';

const stagger = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.08 } },
};

const fadeUp = {
  hidden: { opacity: 0, y: 12 },
  visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease: 'easeOut' } },
};

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function SkeletonCard({ isLead }) {
  return (
    <div className={`${isLead ? 'col-span-full' : ''} animate-pulse`}>
      <div className="flex items-center gap-2 mb-2">
        <div className="h-4 w-20 bg-rule/40 rounded" />
        <div className="h-3 w-32 bg-rule/30 rounded" />
      </div>
      <div className={`${isLead ? 'h-10 w-3/4' : 'h-6 w-2/3'} bg-rule/50 rounded mb-2`} />
      <div className="space-y-2">
        <div className="h-4 w-full bg-rule/30 rounded" />
        <div className="h-4 w-5/6 bg-rule/30 rounded" />
        {isLead && <div className="h-4 w-4/6 bg-rule/30 rounded" />}
      </div>
      <div className="h-3 w-24 bg-gold/30 rounded mt-2" />
    </div>
  );
}

function JobCard({ job, index }) {
  const isLead = index === 0;

  return (
    <motion.article className={`${isLead ? 'col-span-full' : ''} group`} variants={fadeUp}>
      <div className="flex items-center gap-2 mb-2">
        <span className="font-mono text-[10px] px-1.5 py-0.5 border border-rule uppercase tracking-wider">
          {job.source}
        </span>
        <span className="font-mono text-[10px] text-ink/40">
          {job.posted_at ? formatDate(job.posted_at) : formatDate(job.created_at)}
        </span>
      </div>

      <h3
        className={`font-playfair font-bold leading-snug mb-2 ${
          isLead ? 'text-3xl md:text-4xl' : 'text-xl'
        }`}
      >
        <a
          href={job.url}
          target="_blank"
          rel="noopener noreferrer"
          className="group-hover:text-gold hover:underline transition-colors"
        >
          {job.title || 'Untitled Listing'}
          <ChevronRight
            size={isLead ? 20 : 14}
            className="inline ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-gold"
          />
        </a>
      </h3>

      <p
        className={`font-baskerville leading-relaxed text-ink/75 ${
          isLead ? 'text-lg drop-cap' : 'text-sm line-clamp-3'
        }`}
      >
        {job.text || 'No description available.'}
      </p>

      {job.author && (
        <p className="font-mono text-[11px] text-gold mt-2">— {job.author}</p>
      )}
    </motion.article>
  );
}

export default function JobList({ filters = {} }) {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['jobs', filters],
    queryFn: () => fetchJobs(filters),
  });

  if (isLoading) {
    return (
      <section className="px-4 py-6">
        <div>
          <p className="font-mono text-[10px] tracking-[0.3em] uppercase text-ink/40 mb-1">
            Latest Dispatches
          </p>
          <div className="rule-thick mb-6" />
        </div>
        <SkeletonCard isLead />
        <div className="rule-thin my-6" />
        <div className="grid md:grid-cols-2 gap-x-8 gap-y-6">
          {[1, 2, 3, 4].map((i) => (
            <SkeletonCard key={i} isLead={false} />
          ))}
        </div>
      </section>
    );
  }

  if (isError || !data?.data?.length) {
    return (
      <section className="px-4 py-16 text-center">
        <div className="rule-thick mb-8" />
        <p className="font-playfair text-2xl font-bold text-ink/40 mb-2">
          NO LISTINGS FOUND
        </p>
        <p className="font-baskerville italic text-ink/40">
          Check back after next edition
        </p>
        <div className="rule-thick mt-8" />
      </section>
    );
  }

  const jobs = data.data;
  const lead = jobs[0];
  const rest = jobs.slice(1);

  return (
    <motion.section
      className="px-4 py-6"
      variants={stagger}
      initial="hidden"
      animate="visible"
    >
      <motion.div variants={fadeUp}>
        <p className="font-mono text-[10px] tracking-[0.3em] uppercase text-ink/40 mb-1">
          Latest Dispatches
        </p>
        <div className="rule-thick mb-6" />
      </motion.div>

      <motion.div variants={fadeUp}>
        <JobCard job={lead} index={0} />
      </motion.div>

      {rest.length > 0 && (
        <>
          <div className="rule-thin my-6" />
          <div className="grid md:grid-cols-2 gap-x-8 gap-y-6">
            {rest.map((job, i) => (
              <div key={job.id}>
                <JobCard job={job} index={i + 1} />
              </div>
            ))}
          </div>
        </>
      )}
    </motion.section>
  );
}
