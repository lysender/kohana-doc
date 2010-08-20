# Framework Requirements

## PHP 5.2.3 - minimum requirement

Kohana uses features in PHP 5.2.3 such as filter_var, SPL and the likes
which are used in Session class, Validation class and some helper class like
HTML helper.

## Apache 2.x - recommended

It is recommended to use Apache 2.x but Kohana is known to work down to
Apache 1.33.x. However, some Apache directives used by Kohana on its
sample .htaccess does not work, therefore some tweaking is needed.

## mod_rewrite module for Apache - recommended

__mod_rewrite__ module for Apache is needed to create pretty URLs. However,
you can still use Kohana without it but all URL will have a prefix of the
the index.php file.


