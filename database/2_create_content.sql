CREATE TABLE public.content (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    body text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    category character varying(100),
    is_public boolean DEFAULT true,
    author character varying(100)
);