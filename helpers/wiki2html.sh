#!/bin/bash
#set -xv
LANG="de en"
LANG_TARGET=(de_DE en_US)
FILES=( about advanced config doc_complete dwnld install modes new-features npcd pages perfdata_format rrdcached rrd_convert start timeranges tpl_helper tpl_helper_pnp tpl_custom tpl upgrade verify verify_pnp_config webfe_cfg webfe wrapper xport mobile )


DESTDIR="../share/pnp/documents"
URL="http://docs.pnp4nagios.org"

cd $DESTDIR

lindex=0
for L in $LANG; do
    if [ "$L" == "en" ];then
        PART="pnp-0.6"
    else
        PART="$L/pnp-0.6"
    fi

    T=${LANG_TARGET[$lindex]}
    mkdir $T
    index=0
    documents=${#FILES[@]}
    
    while [ "$index" -lt "$documents" ];do
        F=${FILES[$index]}
        echo "$L $F"
        wget -nv -O "${T}/${F}.html" "${URL}/${PART}/${F}?do=export_xhtmlbody"
	sed -i -e's/ü/\&uuml;/g' "${T}/${F}.html"
	sed -i -e's/Ü/\&Uuml;/g' "${T}/${F}.html"
	sed -i -e's/ä/\&auml;/g' "${T}/${F}.html"
	sed -i -e's/Ä/\&Auml;/g' "${T}/${F}.html"
	sed -i -e's/ö/\&ouml;/g' "${T}/${F}.html"
	sed -i -e's/Ö/\&Ouml;/g' "${T}/${F}.html"
        ((index++))

    done
    ((lindex++))
done
rm de_DE/dwnld.html
ln -s en_US/dwnld.html de_DE/dwnld.html
