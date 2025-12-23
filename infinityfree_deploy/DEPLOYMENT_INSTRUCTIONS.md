# InfinityFree Deployment Instructions

## Folder Structure on InfinityFree

```
/home/vol_xxx/
├── htdocs/              <- Public folder (your website root)
│   ├── index.php        <- Use htdocs_index.php (rename it)
│   ├── .htaccess        <- Use htdocs_htaccess.txt (rename it)
│   ├── build/           <- From public/build/
│   ├── images/          <- From public/images/
│   ├── favicon.ico      <- From public/
│   └── robots.txt       <- From public/
│
└── laravel_app/         <- Laravel application (OUTSIDE htdocs)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env             <- Use env_production.txt (rename to .env)
    ├── artisan
    └── composer.json
```

## Step-by-Step Instructions

### 1. Upload laravel_app.zip
1. In InfinityFree File Manager, go to the **root folder** (parent of htdocs)
2. Upload `laravel_app.zip`
3. Extract it - it will create a `laravel_app` folder

### 2. Setup htdocs
1. Go into the `htdocs` folder
2. Delete existing files (keep the folder itself)
3. Upload `htdocs.zip`
4. Extract it

### 3. Configure .env
1. Go to `laravel_app` folder
2. Rename `env_production.txt` to `.env`
3. Edit `.env` and add your APP_KEY (generate one locally with `php artisan key:generate --show`)

### 4. Set Folder Permissions
Make sure storage and bootstrap/cache are writable:
- `laravel_app/storage` - 755 or 775
- `laravel_app/bootstrap/cache` - 755 or 775

### 5. Import Database
1. Go to MySQL Databases in InfinityFree
2. Click phpMyAdmin for your database
3. Import your local database SQL file

## Important Notes
- InfinityFree has PHP limitations (no shell access, limited functions)
- Sessions and cache use FILE driver (not database) for compatibility
- Debug mode is OFF for production
