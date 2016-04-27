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

echo ""
echo "Building ..."
echo ""

javac org/openme_ck/openme_ck.java -classpath json-simple-1.1.1.jar:commons-codec-1.7.jar
if [ "${?}" != "0" ] ; then
  echo "Error: Compilation failed in $PWD!" 
  exit 1
fi

jar cf openme_ck.jar org/openme_ck/openme_ck.class
if [ "${?}" != "0" ] ; then
  echo "Error: Archiving failed in $PWD!" 
  exit 1
fi

echo ""
echo "Installing ..."
echo ""

mkdir ${INSTALL_DIR}/lib
cp -f *.jar ${INSTALL_DIR}/lib
