# ATLAS IPS — Production Deployment Checklist

## Security
- [ ] Create a production `.env` on the server; never commit it.
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Use a unique `APP_KEY` for production.
- [ ] Use a strong, unique admin password.
- [ ] Do not run the production database seeder unless `SEED_ADMIN_PASSWORD` is explicitly set to a strong secret.
- [ ] Web root points to the Laravel `public/` directory.
- [ ] Enable HTTPS.

## Storage
- [ ] Run `php artisan storage:link`.
- [ ] Keep uploaded videos in `storage/app/public/videos/`.
- [ ] Keep reference PDFs in `storage/app/public/references/`.
- [ ] Do not commit uploaded media to Git.
- [ ] Confirm hosting `upload_max_filesize` and `post_max_size` are large enough for the configured video limit (200 MB).

## Laravel production setup
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan migrate --force`
- [ ] `php artisan optimize`
- [ ] `php artisan storage:link`
- [ ] Configure writable `storage/` and `bootstrap/cache/` directories.

## Functional testing
- [ ] Home page loads.
- [ ] Student can open Bab and Materi.
- [ ] Uploaded video plays.
- [ ] YouTube video embeds correctly.
- [ ] Reference link opens.
- [ ] Reference PDF opens.
- [ ] Admin login succeeds with the production account.
- [ ] Non-admin users cannot access `/admin`.
- [ ] Login rate limiting is active.
- [ ] Create/update/delete Bab, Materi, Video and Referensi work.
- [ ] Deleting a Materi/Bab removes its uploaded video/PDF files.

## Backup
- [ ] Export MySQL database.
- [ ] Back up `storage/app/public/videos/`.
- [ ] Back up `storage/app/public/references/`.
