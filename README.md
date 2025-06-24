This is a simple PHP command line to read the Domainbox API for DNS records and create a Bind format to standard ouput, specifically to upload to cloudflare.

Cloudflare currently guesses common DNS records but for complex setups often misses many out resulting in errors. Cloudflare allows uploading in BIND format files ( Advanced DNS ) Domainbox has no BIND file format download

Currently handles DNS records domainbox allow you to set via their interface...
* A
* AAAA
* MX
* CNAME
* TXT 
* NS
* SRV

Version 1 has no error checking.

## Prerequisites

1. **PHP with SOAP extension** - The DomainBox API requires the PHP SOAP extension. Install it with:
   ```bash
   sudo apt-get install php-soap  # For Ubuntu/Debian
   ```

2. **API Access** - You must enable API access in your DomainBox account and whitelist your IP address:
   - Log into DomainBox at https://admin.domainbox.net/
   - Go to https://admin.domainbox.net/account/ip-address/
   - Add your server's IP address to the allowed list
   - Save changes and wait a few minutes for them to take effect

## Usage

```bash
php list.php  --reseller  'your reseller name'  --username 'your user name'  --password 'your password' --domain 'example.com' > mydns.txt
```

### Options

- `--reseller` or `-r`: Your DomainBox reseller name (required)
- `--username` or `-u`: Your DomainBox username (required)
- `--password` or `-p`: Your DomainBox password (required)
- `--domain` or `-d`: The domain to export DNS records for (required)
- `--sandbox` or `-s`: Use sandbox API instead of live API (optional)

### Example

```bash
# Export DNS records for example.com to a file
php list.php --reseller 'myreseller' --username 'myuser' --password 'mypass' --domain 'example.com' > example-dns.txt

# Use sandbox API for testing
php list.php --reseller 'myreseller' --username 'myuser' --password 'mypass' --domain 'example.com' --sandbox > test-dns.txt
```

## Troubleshooting

### Authentication Failed (Error Code 201)

If you get an authentication error, check:

1. Your credentials are correct (reseller, username, password)
2. Your IP address is whitelisted at https://admin.domainbox.net/account/ip-address/
3. API access is enabled for your account
4. Try using `--sandbox` flag if you have sandbox credentials

### No DNS Records Output

If the command runs without errors but produces no output:
- The domain might not have any DNS records configured
- The domain might not exist in your DomainBox account
- Check that you have the correct domain name
