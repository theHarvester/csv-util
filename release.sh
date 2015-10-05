#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag ${TAG}
box build

#
# Copy executable file into GH pages
#
git checkout gh-pages

cp dist/csvutil.phar downloads/csvutil-${TAG}.phar
git add downloads/csvutil-${TAG}.phar

SHA1=$(openssl sha1 dist/csvutil.phar)

JSON='name:"csvutil.phar"'
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"http://mattketmo.github.io/cliph/downloads/cliph-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

#
# Update manifest
#
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
git add manifest.json

git commit -m "Bump version ${TAG}"

#
# Go back to master
#
git checkout master

echo "New version created. Now you should run:"
echo "git push origin gh-pages"
echo "git push ${TAG}"