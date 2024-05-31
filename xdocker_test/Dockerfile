FROM ubuntu
RUN apt update
RUN mkdir -p /tmp/scripts
COPY coolscript.sh /tmp/scripts
WORKDIR /tmp/scripts
CMD ["bash"]

#FROM bash
#RUN mkdir -p /tmp/scripts
#COPY coolscript.sh /tmp/scripts
#WORKDIR /tmp/scripts
#CMD ["bash"]