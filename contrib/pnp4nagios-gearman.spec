Name:         pnp4nagios
Version:      0.6.7
Release:      1
License:      GNU Public License version 2
Packager:     Olivier Raginel <babar@cern.ch>
Vendor:       PNP4nagios team
URL:          http://pnp4nagios.org
Prefix:       /opt/pnp4nagios
Source:       http://github.com/Babar/pnp4nagios/tarball/%{name}-%{version}.tar.gz
Group:        Applications/Monitoring
Requires:     perl(Gearman::Worker), perl(Crypt::Rijndael)
BuildRoot:    %{_tmppath}/%{name}-%{version}-root-%(%{__id_u} -n)
Summary:      Gearman version of pnp4nagios
Provides:     pnp4nagios

%description
From the web page (http://docs.pnp4nagios.org/pnp-0.6/start):

PNP is an addon to Nagios which analyzes performance data provided by plugins
and stores them automatically into RRD-databases (Round Robin Databases, see
RRD Tool).

This is the version with support for Gearman, suitable to use with mod_gearman.

%prep
%setup -q

%build
./configure --with-nagios-user=nagios \
    --with-nagios-group=nagios \
    --prefix=%{_prefix} \
    --libdir=%{_libdir}/%{name} \
    --sysconfdir=%{_sysconfdir}/%{name} \
    --localstatedir=%{_localstatedir} \
    --with-init-dir=%{_initrddir} \
    --with-layout=debian

%{__make} all

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/%{_prefix}

%{__make} install fullinstall DESTDIR=$RPM_BUILD_ROOT INIT_OPTS= INSTALL_OPTS=

%clean
rm -rf $RPM_BUILD_ROOT

%post -p /sbin/ldconfig
%postun -p /sbin/ldconfig

%files
%defattr(-,root,root)
%docdir %{_defaultdocdir}
%{_prefix}
%{_sysconfdir}
%defattr(-,nagios,root)
%{_localstatedir} 

%changelog
* Wed Oct 21 2010 Olivier Raginel <babar@cern.ch>
- First build
