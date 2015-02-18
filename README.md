# SEO Images in Attachment

## OPEN .htaccess

FIND RewriteEngine On

ADD AFTER

RewriteRule ^thumb/(\d+)\.(gif|jpg|jpeg|bmp|png|tiff|tif|tga)$ download/file.php?id=$1&t=1 [NC]

RewriteRule ^pic/(\d+)\.(gif|jpg|jpeg|bmp|png|tiff|tif|tga)$ download/file.php?id=$1&mode=view [NC]

RewriteRule ^small/(\d+)\.(gif|jpg|jpeg|bmp|png|tiff|tif|tga)$ download/file.php?id=$1 [NC]

RewriteRule ^img/(\d+)\.(gif|jpg|jpeg|bmp|png|tiff|tif|tga)$ download/file.php?id=$1 [NC]
