rm org/openme/openme.class
rm openme.jar

javac org/openme/openme.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme.jar org/openme/openme.class
