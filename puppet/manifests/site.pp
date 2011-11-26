node 'ecuador.thirdsectordesign.org' inherits testserver { # a production server with MySQL
}

node testserver {
  include common, apache, php, mysql, munin, civihosting  
}

class common{
  package { ["emacs23-nox", "git-core"] :
    ensure => present      
  }
}

class apache{
  package { ['apache2-mpm-worker', 'libapache2-mod-fcgid'] :
    ensure => present
  }
  file { "/etc/apache/httpd.conf":
      mode => 644,
      owner => root,
      group => root,
      source => "puppet:///modules/apache/httpd.conf"
  }
}

class php{
  package { ['php-apc', 'php-elisp', 'php-cgi', 'php-cli', 'php5-mysql'] :
    ensure => present
  }
  file { "/var/www/phpinfo.php":
      mode => 644,
      owner => root,
      group => root,
      source => "puppet:///modules/php/phpinfo.php"
  }
  file { "/var/www/apc.php":
      mode => 644,
      owner => root,
      group => root,
      source => "puppet:///modules/php/phpinfo.php"
  }
  
}

class mysql{
  package { 'mysql-server':
    ensure => present
  }
}

class munin{
  package { 'apache' :
    ensure => present
  }
}

# class civihosting{
#   package { 'apache' :
#     ensure => present
#   }
# }

# class clients{
#   package { 'apache' :
#     ensure => present
#   }
# }

# class xdebug{ # for test servers
#   package { 'apache' :
#     ensure => present
#   }
# }





# BELOW THIS LINE IS OLD CONFIGURATION - NEEDS DELETING


# node default {
# 
#    # really necessary at this high level? if so, also add php-elisp
#   
#   # TODO work out how to properly implement monitoring
#   
# }
# 
# node webserver inherits default {
# 
#   include apache, php, mysql, postfix
#   
#   # set /etc/aliases to be root: michaelmcandrew@thirdsectordesign.org and subscribe newaliases or somin
#   
#   # do we also inlcude a .my.cnf file?
#   
#   # package { "drush" : ensure => present } # need to replace this with actually downloading the latest drush and setting it up according to drush installation instructions in the drush readme
#   
#   package { "php-apc" : ensure => present }
#   package { "php5-gd" : ensure => present }
#   package { "php5-curl" : ensure => present }
#   # i think that the above php modules can be enabled using the php::module { mysql : } syntax below
#   
#   php::module { mysql : }
#   # TODO need to work out how to enable mod_ssl and mod_rewrite
#   # could be like this: https://github.com/puppet-modules/puppet-apache/raw/master/manifests/init.pp
#   # or maybe there is the 42 way of doing it
#   
#   
# 
# }
# 
# node productionserver inherits webserver{
#   # TODO need to ensure that backups (inc. remote backups) are happening (currently can be handled with civihosting scripts)
#   # TODO need to also download the civihosting scripts to handle cron (or switch to aegir!)
#   # need to create fstab that mounts /dev/sdf on /backup ?? really?? - maybe need to get more sophisticated
#   # need to add access to mysql from remote hosts as specified by Rob Stead (/etc/mysql/my.cnf don't bind to localhost and add access access for specific users from specific IPs)  
# }
# 
# node 'argentina.thirdsectordesign.org' inherits webserver {
# 
# }
# node 'bolivia.thirdsectordesign.org' inherits default {
#   # need to create fstab that mounts /dev/sdf on /backup
#   # note i mistakenly installed the webserver config on this by making bolivia inherit webserver.  I'm not sure if this will be removed when i say it just inherits default
#   # todo - recreate bolivia so it doesn't have the unecc. stuff on it.
# }
# 
# node 'brazil.thirdsectordesign.org' inherits webserver {
#   # need to create fstab that mounts /dev/sdf on /backup
#   # redirect all postfix mail to NULL
# }
# 
# node 'colombia.thirdsectordesign.org' inherits webserver {
#   # need to create fstab that mounts /dev/sdf on /backup
#   # redirect all postfix mail to NULL
# }