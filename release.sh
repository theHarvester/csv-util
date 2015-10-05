#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Deploy the latest documentation
#
echo "Deploying documentation..."
mkdocs gh-deploy

#
# Tag & build master branch
#
echo "Tagging build..."
git checkout master
git tag ${TAG}
box build

#
# Copy executable file into GH pages
#
echo "Moving everything into place..."
#git checkout gh-downloads

cp csvutil.phar downloads/csvutil-${TAG}.phar
git add downloads/csvutil-${TAG}.phar

SHA1=$(openssl sha1 csvutil.phar)

JSON='name:"csvutil.phar"'
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"http://theharvester.github.io/csv-util/downloads/csvutil-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

#
# Update manifest
#
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
echo "Committing and pushing..."
git add manifest.json

git commit -m "Releasing version ${TAG}"
git push origin master

#
# Go back to master
#
git checkout gh-pages
git checkout master -- downloads
git checkout master -- manifest.json
git add -A
git commit -m "Releasing version ${TAG}"
git push origin gh-pages
git push --tags

echo "New version ${TAG} created"