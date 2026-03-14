export async function fetchJobs({ limit = 50, offset = 0, source, search } = {}) {
  const params = new URLSearchParams();
  params.set('limit', limit);
  params.set('offset', offset);
  if (source) params.set('source', source);
  if (search) params.set('search', search);

  const res = await fetch(`/api/jobs?${params}`);
  if (!res.ok) throw new Error(`Failed to fetch jobs: ${res.status}`);
  return res.json();
}

export async function triggerParse(source = 'hackernews') {
  const res = await fetch('/api/parse', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ source }),
  });
  if (!res.ok) throw new Error(`Failed to trigger parse: ${res.status}`);
  return res.json();
}
