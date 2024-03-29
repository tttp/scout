node 'argentina.thirdsectordesign.org' inherits webserver { # a production server with MySQL
}

node '*.webserver.thirdsectordesign.org' inherits webserver { # a production server with MySQL
}


node webserver {
  include common, apache, php, mysql, munin, scout  
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
  file { "/etc/apache2/httpd.conf":
      mode => 644,
      owner => root,
      group => root,
      source => "puppet:///modules/apache/httpd.conf"
  }
}

class php{
  package { ['php-apc', 'php-elisp', 'php5-cgi', 'php5-cli', 'php5-mysql', 'php5-gd'] :
    ensure => present
  }
  file { "/var/www/phpinfo.php":
      mode => 644,
      owner => root,
      group => root,
      source => "puppet:///modules/php/phpinfo.php"
  }  
}

class mysql{
}

class munin{
}

class scout{
}

class xdebug{ # for test servers
}


