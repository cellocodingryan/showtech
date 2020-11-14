#docker pull mongo
#docker pull node
#docker network create showtech-network
#docker run -d --network showtech-network -p 27017:27017 --rm --name database mongo
docker build . -t showtech_web
sudo docker stop showtechs
sudo docker run -it  --network showtech-network --name showtechs -p 5000:5000 --rm showtech_web
