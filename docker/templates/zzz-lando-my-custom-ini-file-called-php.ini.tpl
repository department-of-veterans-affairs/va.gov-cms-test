[PHP]

;;;;;;;;;;;;;;;
; PHP Globals ;
;;;;;;;;;;;;;;;

short_open_tag = Off
output_buffering = 4096
allow_call_time_pass_reference = Off
variables_order = "GPCS"
request_order = "GP"
register_long_arrays = Off
register_argc_argv = Off
magic_quotes_gpc = Off
enable_dl = Off
allow_url_fopen = On
realpath_cache_size = "800K"
realpath_cache_ttl = "86400"
disable_functions =
sendmail_path=/usr/sbin/ssmtp -t
;include_path = ".:/usr/share/pear:/usr/share/php"

[Date]
date.timezone = "UTC"

;;;;;;;;;;;;;;;;;;;;;;
;; PACKAGE SETTINGS ;;
;;;;;;;;;;;;;;;;;;;;;;

; Xdebug
xdebug.max_nesting_level = 256
xdebug.show_exception_trace = 0
xdebug.collect_params = 0

; Globals
expose_php = on
max_execution_time = 90
max_input_time = 900
max_input_vars = 10000
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
error_reporting = E_ALL & ~E_DEPRECATED
ignore_repeated_errors = on
html_errors = off
display_errors = on
log_errors = on