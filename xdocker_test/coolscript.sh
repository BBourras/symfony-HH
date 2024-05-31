#!/bin/bash
counter=3
message="Bonjour Toto Lasticot"

for i in $(seq $counter)
do
  echo "$i: $message"
  sleep 1
done
