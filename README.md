# Notes regarding SELinux

If you are using a Linux distribution that have SELinux enabled by default, there is one thing you'd have to do before you can begin. The SELinux context for the cache directory have to be changed from `httpd_sys_content_t` to `httpd_sys_rw_content_t`, otherwise the framework won't be able to cache files.

This is done by executing the following commands as root:

```bash
chcon -R -t httpd_sys_rw_content_t '$FIX_TARGET_PATH'
```

The `$FIX_TARGET_PATH` have to be the absolute path to the cache directory.