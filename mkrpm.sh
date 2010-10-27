#!/bin/bash
name=pnp4nagios
version=0.6.7
tar=$name-$version.tar.gz 

mkdir -p RPMBUILD/{RPMS/{i386,i586,i686,x86_64},SPECS,BUILD,SOURCES,SRPMS}
mkdir $name-$version
rsync -a config.guess config.sub configure configure.ac \
    contrib helpers include install-sh \
    lib Makefile.in man sample-config scripts \
    share src subst.in summary.in $name.spec $name-$version

tar zcf $tar $name-$version/
rm -rf $name-$version/

#mv $tar RPMBUILD/SOURCES/
#rpmbuild --define "_topdir $PWD/RPMBUILD" -ba $name.spec
rpmbuild --define "_topdir $PWD/RPMBUILD" -tb $tar

find RPMBUILD/ -type f -name "*.rpm" -exec mv -v {} . \;
rm -rf RPMBUILD/ # $tar
