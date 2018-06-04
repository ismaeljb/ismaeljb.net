# Ejemplo de uso: ./crear_enlaces.sh Escocia_Sep_2012/JB anaftp/Fotos/Escocia_2012

album=$1
carpeta=$2

#`ls ./albums/${1}/*.JPG`
if [ $? -ne 0 ]
then
  echo "Uso: $0 ALBUM AUTOR CARPETA"
  exit 1
fi

mkdir $2

chmod u+r ./albums/${1}/

j=0; 
for i in `ls ./albums/${1}/*.JPG` 
do 
#ln $i ./${2}/$j.jpg
foto=`basename ${i}`
ln $i ./${2}/${foto}
j=$((j+1)) 
done

chmod u-r ./albums/${1}/
