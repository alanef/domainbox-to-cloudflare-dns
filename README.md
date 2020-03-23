This is a simple PHP command line to read the Domainbox API for DNS records and create a Bind format file, specifically to upload to cloudflare.

Cloudflare currently guesses common DNS records but for complex setups often misses many out resulting in errors. Cloudflare allows uploading in BIND format files ( Advanced DNS ) Domainbox has no BIND file format download

Currently only handles
A
MX
CNAME
TXT 
records

Version 1 has no error checking.

Usage

php list.php  --reseller  'your reseller name'  --username 'your user name'  --password 'your password'
