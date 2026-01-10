# Fileinfo Extension Required

## Issue
The application requires PHP's `fileinfo` extension to be enabled on the server. Without it, you may encounter the error:
```
Class "finfo" not found
```

## Solution: Enable fileinfo Extension

### For cPanel/WHM Servers:
1. Log into cPanel
2. Go to "Select PHP Version" or "PHP Selector"
3. Enable the `fileinfo` extension
4. Save changes

### For Ubuntu/Debian Servers:
```bash
sudo apt-get install php-fileinfo
# or for specific PHP version
sudo apt-get install php8.1-fileinfo  # Replace 8.1 with your PHP version
sudo systemctl restart php-fpm  # or apache2/nginx depending on your setup
```

### For CentOS/RHEL Servers:
```bash
sudo yum install php-fileinfo
# or for specific PHP version
sudo yum install php81-fileinfo  # Replace 81 with your PHP version
sudo systemctl restart php-fpm
```

### Manual PHP.ini Configuration:
1. Find your `php.ini` file:
   ```bash
   php --ini
   ```

2. Edit `php.ini` and ensure this line is not commented out:
   ```ini
   extension=fileinfo
   ```

3. Restart your web server:
   ```bash
   sudo systemctl restart php-fpm
   sudo systemctl restart apache2  # or nginx
   ```

### Verify Installation:
Run this command to verify fileinfo is enabled:
```bash
php -m | grep fileinfo
```

If it's enabled, you should see `fileinfo` in the output.

## Temporary Workaround
The application has been updated to handle this error gracefully:
- Photo deletion uses direct file operations instead of Storage methods
- Error handling has been added to catch finfo errors
- A custom MIME type detector fallback has been implemented

However, **enabling the fileinfo extension is the recommended solution** for full functionality.

