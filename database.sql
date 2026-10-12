-- ==========================================================================
--  hika — schema and starting content
--
--  Import with:  mysql -uroot -proot hika < database.sql
--
--  Column names are single words on purpose (no `_at`, no `_id`): a project
--  has a `created`, a social has a `sort`, a message has an `ip` and an
--  `agent`. Table names are plural; the one exception is `profile`, which is
--  a single row describing the owner of the site.
-- ==========================================================================

SET NAMES utf8mb4;

DROP TABLE IF EXISTS messages, timeline, projects, skills, socials, users, profile;

-- --------------------------------------------------------------------------
-- profile — the one row that describes Hiswill.
-- --------------------------------------------------------------------------
CREATE TABLE profile (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name      VARCHAR(80)  NOT NULL,
  alias     VARCHAR(40)  NOT NULL DEFAULT '',
  title     VARCHAR(120) NOT NULL,
  tagline   VARCHAR(200) NOT NULL DEFAULT '',
  bio       TEXT         NOT NULL,
  about     MEDIUMTEXT   NOT NULL,
  location  VARCHAR(120) NOT NULL DEFAULT '',
  campus    VARCHAR(160) NOT NULL DEFAULT '',
  degree    VARCHAR(160) NOT NULL DEFAULT '',
  email     VARCHAR(160) NOT NULL DEFAULT '',
  phone     VARCHAR(40)  NOT NULL DEFAULT '',
  avatar    VARCHAR(200) NOT NULL DEFAULT '',
  resume    VARCHAR(200) NOT NULL DEFAULT '',
  available TINYINT(1)   NOT NULL DEFAULT 1,
  updated   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- users — who may open the admin panel.
-- --------------------------------------------------------------------------
CREATE TABLE users (
  id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name     VARCHAR(80)  NOT NULL,
  email    VARCHAR(160) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role     ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  status   TINYINT(1)   NOT NULL DEFAULT 1,
  login    TIMESTAMP    NULL DEFAULT NULL,
  created  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- socials — one row per network. `url` may be empty for a network with no
-- public profile, in which case the handle is shown as a copyable chip.
-- --------------------------------------------------------------------------
CREATE TABLE socials (
  id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name   VARCHAR(40)  NOT NULL,
  handle VARCHAR(80)  NOT NULL,
  url    VARCHAR(255) NOT NULL DEFAULT '',
  icon   VARCHAR(60)  NOT NULL DEFAULT '',
  color  VARCHAR(20)  NOT NULL DEFAULT '',
  sort   INT UNSIGNED NOT NULL DEFAULT 0,
  shown  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- skills — grouped by `category`. `level` is 0..100 for a bar, or 0 to show
-- a plain chip (which is what the interests use).
-- --------------------------------------------------------------------------
CREATE TABLE skills (
  id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name     VARCHAR(80)  NOT NULL,
  category ENUM('language','stack','interest','practice') NOT NULL DEFAULT 'language',
  level    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  icon     VARCHAR(60)  NOT NULL DEFAULT '',
  note     VARCHAR(200) NOT NULL DEFAULT '',
  sort     INT UNSIGNED NOT NULL DEFAULT 0,
  shown    TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- projects — the work, listed on /projects and opened at /projects/<slug>.
-- --------------------------------------------------------------------------
CREATE TABLE projects (
  id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title    VARCHAR(120) NOT NULL,
  slug     VARCHAR(140) NOT NULL,
  summary  VARCHAR(280) NOT NULL DEFAULT '',
  body     MEDIUMTEXT   NOT NULL,
  tech     VARCHAR(255) NOT NULL DEFAULT '',
  category VARCHAR(40)  NOT NULL DEFAULT 'web',
  repo     VARCHAR(255) NOT NULL DEFAULT '',
  live     VARCHAR(255) NOT NULL DEFAULT '',
  icon     VARCHAR(60)  NOT NULL DEFAULT 'fa-solid fa-code',
  featured TINYINT(1)   NOT NULL DEFAULT 0,
  status   ENUM('published','draft') NOT NULL DEFAULT 'published',
  sort     INT UNSIGNED NOT NULL DEFAULT 0,
  created  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY projects_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- timeline — education and the journey, newest first via `sort`.
-- --------------------------------------------------------------------------
CREATE TABLE timeline (
  id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title  VARCHAR(120) NOT NULL,
  place  VARCHAR(160) NOT NULL DEFAULT '',
  period VARCHAR(60)  NOT NULL DEFAULT '',
  detail VARCHAR(280) NOT NULL DEFAULT '',
  type   ENUM('education','work','milestone') NOT NULL DEFAULT 'milestone',
  sort   INT UNSIGNED NOT NULL DEFAULT 0,
  shown  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- messages — what the contact form writes.
-- --------------------------------------------------------------------------
CREATE TABLE messages (
  id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name    VARCHAR(80)  NOT NULL,
  email   VARCHAR(160) NOT NULL,
  subject VARCHAR(160) NOT NULL DEFAULT '',
  body    TEXT         NOT NULL,
  ip      VARCHAR(45)  NOT NULL DEFAULT '',
  agent   VARCHAR(255) NOT NULL DEFAULT '',
  seen    TINYINT(1)   NOT NULL DEFAULT 0,
  created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================================
--  STARTING CONTENT
-- ==========================================================================

INSERT INTO profile
  (name, alias, title, tagline, bio, about, location, campus, degree, email, phone, available)
VALUES (
  'Hiswill Kato',
  'Hika',
  'Tech Enthusiast',
  'A tech enthusiast who writes clean, simple code — building web software, scripting on Linux, and learning the cloud one service at a time.',
  'I am Hiswill Kato — Hika to my friends — a tech enthusiast and a Computer Engineering student at Busitema University in Uganda. I started writing code in 2020 and have been tinkering ever since, from web software to Linux servers and the cloud.',
  'I am Hiswill Kato — Hika to my friends — a tech enthusiast and a Bachelor of Science in Computer Engineering student at Busitema University in Uganda.

I started writing code in 2020, curious about how a website actually works. That curiosity never stopped: it widened into a love for everything around the code — Linux, the command line, servers, the cloud, and the small details that make a system feel simple.

## How I like to build
I reach for the smallest tool that does the job, and I keep code clean, unduplicated and easy to read. For web software that means plain PHP and MySQL; for the repetitive work, Bash; for the interface, Bootstrap 5. The next person — often future me — should be able to read it and know exactly what it does.

## Where I am heading
I am learning the cloud hands-on and going deep into AWS. I keep exploring Linux too — different distributions, how they are put together, and how to set them up from scratch. Locally I run Ubuntu 26.04 in WSL; in the cloud, Ubuntu 24.04 LTS. I like breaking my own setup just so I can put it back together and understand it a little better.

## Beyond the keyboard
I am into artificial intelligence, systems security and open-source software. I enjoy installing operating systems and software, wiring up servers, and turning curiosity into something that actually works.',
  'Bbanda, Mityana, Uganda',
  'Busitema University',
  'BSc. Computer Engineering',
  'hiswillkato@gmail.com',
  '+256751884466',
  1
);

-- The first account. Password is `Hiswill256@` — change it after signing in.
INSERT INTO users (name, email, password, role, status) VALUES
('Hiswill Kato', 'hiswillkato@gmail.com', '$2y$12$7XgFbdiSkYgvRwCcU2ShUuQPWaCNy83fkKGPVZhH9/fYMWig5eRhm', 'admin', 1);

INSERT INTO socials (name, handle, url, icon, color, sort, shown) VALUES
('GitHub',      '@HiswillKato', 'https://github.com/HiswillKato',        'fa-brands fa-github',       '#e6edf3', 1, 1),
('X (Twitter)', '@HiswillKato', 'https://x.com/HiswillKato',             'fa-brands fa-x-twitter',    '#e6edf3', 2, 1),
('LinkedIn',    '@HiswillKato', 'https://www.linkedin.com/in/HiswillKato','fa-brands fa-linkedin-in', '#3b9dff', 3, 1),
('Facebook',    '@HiswillKato', 'https://facebook.com/HiswillKato',      'fa-brands fa-facebook-f',   '#4f8ef7', 4, 1),
('Instagram',   '@HiswillKato', 'https://instagram.com/HiswillKato',     'fa-brands fa-instagram',    '#e1568f', 5, 1),
('YouTube',     '@HiswillKato', 'https://youtube.com/@HiswillKato',      'fa-brands fa-youtube',      '#ff4b4b', 6, 1),
('WhatsApp',    '+256751884466','https://wa.me/256751884466',            'fa-brands fa-whatsapp',     '#3ddc84', 7, 1);

INSERT INTO skills (name, category, level, icon, note, sort) VALUES
-- Languages
('PHP',           'language', 88, 'fa-brands fa-php',      'Server-side web software', 1),
('HTML',          'language', 93, 'fa-brands fa-html5',    '', 2),
('CSS',           'language', 86, 'fa-brands fa-css3-alt', '', 3),
('JavaScript',    'language', 78, 'fa-brands fa-js',       'Plain JS, no framework', 4),
('SQL / MySQL',   'language', 82, 'fa-solid fa-database',  'Prepared statements', 5),
('Bash',          'language', 68, 'fa-solid fa-terminal',  'Automation & scripting', 6),
('C / C++',       'language', 35, 'fa-solid fa-code',      'Currently learning', 7),
-- Tools and stack
('LAMP Stack',    'stack', 88, 'fa-solid fa-server',       'Linux, Apache, MySQL, PHP', 1),
('Bootstrap 5',   'stack', 90, 'fa-brands fa-bootstrap',   '', 2),
('Font Awesome',  'stack', 90, 'fa-brands fa-font-awesome','', 3),
('Git & GitHub',  'stack', 80, 'fa-brands fa-git-alt',     '', 4),
('Apache',        'stack', 78, 'fa-solid fa-network-wired', 'Virtual hosts & rewrites', 5),
('Linux',         'stack', 82, 'fa-brands fa-linux',       'Ubuntu 26.04 (WSL) & 24.04 LTS (cloud)', 6),
('AWS',           'stack', 40, 'fa-brands fa-aws',         'Cloud — currently learning', 7),
-- Interests
('Linux',              'interest', 0, 'fa-brands fa-linux',          'Distro exploring', 1),
('Cloud Computing',    'interest', 0, 'fa-solid fa-cloud',           'AWS, learning hands-on', 2),
('Bash & Automation',  'interest', 0, 'fa-solid fa-terminal',        'Scripting the boring parts', 3),
('Artificial Intelligence','interest', 0, 'fa-solid fa-brain',       'Learning & exploring', 4),
('Systems Security',   'interest', 0, 'fa-solid fa-shield-halved',   'Cybersecurity', 5),
('Web Development',    'interest', 0, 'fa-solid fa-globe',           '', 6),
('Open Source',        'interest', 0, 'fa-brands fa-osi',            'Sharing what I build', 7),
('Windows',            'interest', 0, 'fa-brands fa-windows',        '', 8),
-- Hands-on
('OS Installation',        'practice', 0, 'fa-solid fa-compact-disc', '', 1),
('Software Installation',  'practice', 0, 'fa-solid fa-download',     '', 2),
('Server Setup',           'practice', 0, 'fa-solid fa-hard-drive',   'Apache, PHP & MariaDB on Linux', 3),
('Cloud Deployment',       'practice', 0, 'fa-solid fa-cloud-arrow-up','VMs, SSH & the cloud console', 4),
('Troubleshooting',        'practice', 0, 'fa-solid fa-wrench',       'Tinkering until it works', 5);

INSERT INTO projects (title, slug, summary, body, tech, category, repo, live, icon, featured, status, sort) VALUES
(
  'BUBAPC',
  'bubapc',
  'A fellowship website for the Born Again Pentecostal Church at Busitema University — leadership, departments, families, events and daily devotionals.',
  'BUBAPC is a full website for the Busitema University Born Again Pentecostal Church, a Christian fellowship for born-again and Pentecostal students. The goal is a place where the fellowship stays connected and where a visitor can quickly learn who the church is.

## What it does
- A public side with leadership, departments, families, events, daily devotionals and announcements.
- A member area with sign-in and a personal profile.
- An admin panel where a team can manage all of the above without touching code.

## How it is built
Plain PHP with MySQLi prepared statements, Bootstrap 5 for the interface and Font Awesome for icons. Apache does the routing through .htaccess, so every page has a short address and the working parts stay off the web.',
  'PHP, MySQLi, MariaDB, Bootstrap 5, Apache',
  'web',
  'https://github.com/HiswillKato/BUBAPC',
  '',
  'fa-solid fa-church',
  1,
  'published',
  1
),
(
  'Gospelzora',
  'gospelzora',
  'A gospel music website for discovering songs and the artists behind them — built to give faith music a welcoming, easy-to-navigate home.',
  'Gospelzora is a gospel music website built around a simple idea: give gospel music a welcoming space where listeners can find songs and learn more about the artists behind them.

## What it does
- Browse gospel songs and explore artists.
- Search for music and related content.
- Clear artist and song pages with artwork and information.
- An administration area for managing the content shown to visitors.

## Why it exists
Gospel music brings people together and offers hope. I wanted to build something that reflects that experience while giving me room to grow as a developer.',
  'PHP, MySQLi, Bootstrap 5, JavaScript',
  'web',
  'https://github.com/HiswillKato/Gospelzora',
  '',
  'fa-solid fa-music',
  1,
  'published',
  2
),
(
  'SiFM — Simple File Manager',
  'sifm',
  'An open-source, single-file web file manager written in PHP. Browse, edit, upload, zip and manage files right from the browser. MIT licensed.',
  'SiFM (Simple File Manager) is an open-source, web-based PHP file manager that manages files and folders through a simple interface. It is deliberately lightweight: one PHP file you drop on a server and use.

## What it does
- View, create, edit, upload, download and delete files and folders.
- Copy, move and rename, with ZIP download and extraction.
- Change file permissions (chmod) for multi-user setups.
- Create ZIP backups and restore them.
- Search by keyword or wildcard, and check PHP syntax from the interface.
- Detailed file information: size, MIME type, modification date and ownership.

## License
SiFM is released under the MIT License, so you are free to use, change and share it. Contributions and ideas are welcome — reach out any time.',
  'PHP, HTML, CSS, JavaScript',
  'tool',
  'https://github.com/HiswillKato/SiFM',
  '',
  'fa-solid fa-folder-tree',
  1,
  'published',
  3
);

INSERT INTO timeline (title, place, period, detail, type, sort) VALUES
('BSc. Computer Engineering', 'Busitema University', 'Present', 'Studying computer engineering and building software alongside the course.', 'education', 1),
('Freelance Web Developer', 'Freelance & personal projects', '2024 – Present', 'Building and maintaining PHP and MySQL websites, scripting the repetitive parts in Bash, and shipping them to Linux servers.', 'work', 2),
('Learning the cloud', 'AWS & self-study', '2025 – Present', 'Going deep into AWS services, Linux servers and the command line — one hands-on project at a time.', 'milestone', 3),
('Wrote my first lines of code', 'Self-taught', '2020', 'Started with HTML and PHP, curious about how a website actually works. Never really stopped.', 'milestone', 4);
