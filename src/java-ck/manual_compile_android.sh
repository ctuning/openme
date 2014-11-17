#
# OpenME for Collective Knowledge
#
# See LICENSE.txt for licensing details.
# See Copyright.txt for copyright details.
#
# Developer: Grigori Fursin
#

rm org/openme_ck/openme_ck.class
rm openme_ck.jar

rem Set your own path
export JAVA_HOME=/home/fursin/fggwork/java/jdk1.6.0_41
export PATH=$JAVA_HOME/bin:$PATH

javac org/openme_ck/openme_ck.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme_ck.jar org/openme_ck/openme_ck.class
