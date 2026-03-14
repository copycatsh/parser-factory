CREATE TABLE IF NOT EXISTS jobs (
    id SERIAL PRIMARY KEY,
    hn_id VARCHAR(50) UNIQUE,
    source VARCHAR(50) NOT NULL,
    author VARCHAR(255),
    title TEXT,
    text TEXT,
    url VARCHAR(500),
    posted_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_jobs_source ON jobs(source);
CREATE INDEX idx_jobs_posted_at ON jobs(posted_at);
