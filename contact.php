<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$me     = me();
$errors = [];
$old    = ['name' => '', 'email' => '', 'subject' => '', 'body' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $old = [
        'name'    => mb_substr(post('name'), 0, 80),
        'email'   => mb_substr(post('email'), 0, 160),
        'subject' => mb_substr(post('subject'), 0, 160),
        'body'    => mb_substr(post('body'), 0, 5000),
    ];

    /* A hidden box nobody should fill in. A bot fills it, and the message is
       quietly dropped instead of saved. */
    if (post('website') !== '') {
        flash('Thank you — your message has been sent.');
        redirect('contact');
    }

    if ($old['name'] === '') {
        $errors[] = 'Please tell me your name.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'That email address does not look right.';
    }
    if (mb_strlen($old['body']) < 5) {
        $errors[] = 'Please write a short message.';
    }

    if ($errors === []) {
        add(
            'INSERT INTO messages (name, email, subject, body, ip, agent) VALUES (?, ?, ?, ?, ?, ?)',
            [
                $old['name'],
                $old['email'],
                $old['subject'],
                $old['body'],
                $_SERVER['REMOTE_ADDR'] ?? '',
                mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]
        );

        flash('Thank you — your message has been sent. I\'ll reply as soon as I can.');
        redirect('contact');
    }
}

$title       = 'Contact';
$section     = 'contact';
$description = 'Have a project, a question or an idea worth talking about? Send me a message — I read every one and reply as soon as I can.';

require __DIR__ . '/app/parts/header.php';
?>

<section class="container py-5">
    <div class="row g-4 g-lg-5">
        <!-- ---------- form ---------- -->
        <div class="col-lg-7">
            <div class="card p-4 p-lg-5">
                <?= notice() ?>

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <h2 class="h4 mb-1">Send a message</h2>
                <p class="text-body-secondary mb-4">All fields marked with * are required.</p>

                <form method="post" action="<?= url('contact') ?>" novalidate>
                    <?= csrf() ?>
                    <div class="visually-hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="name">Your name *</label>
                            <input class="form-control" id="name" name="name" value="<?= e($old['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="email">Email *</label>
                            <input class="form-control" id="email" name="email" type="email" value="<?= e($old['email']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="subject">Subject</label>
                            <input class="form-control" id="subject" name="subject" value="<?= e($old['subject']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="body">Message *</label>
                            <textarea class="form-control" id="body" name="body" rows="6" required><?= e($old['body']) ?></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <?= icon('fa-solid fa-paper-plane') ?> Send message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ---------- details ---------- -->
        <div class="col-lg-5">
            <span class="eyebrow">Get in touch</span>
            <h1 class="display-4 mt-2 mb-3">Let's talk</h1>
            <p class="text-body-secondary mb-4">
                Whether it's a project, a question, a Linux tip, or just a hello — my
                inbox is always open. I usually reply within a day.
            </p>

            <div class="d-grid gap-3 mb-4">
                <a class="contact-line" href="mailto:<?= e($me['email'] ?? EMAIL) ?>">
                    <span class="contact-icon"><?= icon('fa-solid fa-envelope') ?></span>
                    <span>
                        <span class="d-block text-body-secondary small">Email</span>
                        <span class="fw-semibold"><?= e($me['email'] ?? EMAIL) ?></span>
                    </span>
                </a>
                <a class="contact-line" href="tel:<?= e(preg_replace('/\s+/', '', $me['phone'] ?? '')) ?>">
                    <span class="contact-icon"><?= icon('fa-solid fa-phone') ?></span>
                    <span>
                        <span class="d-block text-body-secondary small">Phone</span>
                        <span class="fw-semibold"><?= e($me['phone'] ?? '') ?></span>
                    </span>
                </a>
                <a class="contact-line" href="https://wa.me/<?= e(preg_replace('/\D/', '', $me['phone'] ?? '')) ?>" target="_blank" rel="noopener">
                    <span class="contact-icon"><?= icon('fa-brands fa-whatsapp') ?></span>
                    <span>
                        <span class="d-block text-body-secondary small">WhatsApp</span>
                        <span class="fw-semibold">Chat with me</span>
                    </span>
                </a>
                <div class="contact-line">
                    <span class="contact-icon"><?= icon('fa-solid fa-location-dot') ?></span>
                    <span>
                        <span class="d-block text-body-secondary small">Based in</span>
                        <span class="fw-semibold"><?= e($me['location'] ?? '') ?></span>
                    </span>
                </div>
            </div>

            <?php require __DIR__ . '/app/parts/socials.php'; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
