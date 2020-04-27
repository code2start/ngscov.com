#!/bin/bash
extension=$1
run=$2     #run =provided by the user by default it is  "mandatory"
path=$3    # provided by the dev team
adapter=$4 # Scroll button with multiple choice
#NexteraPE
#TruSeq2
#TruSeq3
#TruSeq3
#file.fasta
#cd $path
#file extension default is written # cherif_R1_001.fastq.gz # cherif_R2_001.fastq.gz
ls ./$path/*$extension | sed "s/$extension//" >$path/samp.txt
#ls ./$path >$path/samp.txt
file=$path/samp.txt
cd $path
touch fileresult1.txt
touch fileresult2.txt
for F in $(cat samp.txt); do
    SAMP=$(basename "$F")
    echo $SAMP >>fileresult1.txt
    echo $adapter >>fileresult2.txt
done
paste fileresult1.txt fileresult2.txt >result.txt
