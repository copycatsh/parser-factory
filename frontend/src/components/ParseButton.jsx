import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Printer } from 'lucide-react';
import { triggerParse } from '../api/client';

export default function ParseButton() {
  const queryClient = useQueryClient();
  const [showSuccess, setShowSuccess] = useState(false);

  const { mutate, isPending } = useMutation({
    mutationFn: () => triggerParse('hackernews'),
    onSuccess: () => {
      setShowSuccess(true);
      queryClient.invalidateQueries({ queryKey: ['jobs'] });
      setTimeout(() => setShowSuccess(false), 2000);
    },
  });

  const label = showSuccess ? 'PUBLISHED ✓' : isPending ? 'PRINTING...' : 'PUBLISH NEW EDITION';

  return (
    <button
      onClick={() => mutate()}
      disabled={isPending}
      className="flex items-center gap-2 font-mono text-[11px] tracking-wider uppercase
        px-3 py-1.5 border border-ink/30 hover:border-gold hover:text-gold
        transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <Printer size={13} strokeWidth={1.5} className={isPending ? 'animate-pulse' : ''} />
      {label}
    </button>
  );
}
