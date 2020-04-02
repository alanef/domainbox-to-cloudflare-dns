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

Usage

```php list.php  --reseller  'your reseller name'  --username 'your user name'  --password 'your password' --domain 'example.com' > mydns.txt```
