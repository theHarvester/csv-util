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
echo "Deploying documentation...\n"
mkdocs gh-deploy

#
# Tag & build master branch
#
echo "Tagging build...\n"
git checkout master
git tag ${TAG}
box build

#
# Copy executable file into GH pages
#
echo "Moving everything into place...\n"
git checkout gh-pages

cp csvutil.phar downloads/csvutil-${TAG}.phar
git add downloads/csvutil-${TAG}.phar

SHA1=$(openssl sha1 csvutil.phar)

JSON='name:"csvutil.phar"'
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"http://mattketmo.github.io/cliph/downloads/cliph-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

#
# Update manifest
#
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
echo "Committing and pushing...\n"
git add manifest.json

git commit -m "Bump version ${TAG}"

#
# Go back to master
#
git checkout master

git push origin gh-pages
git push --tags

echo "New version ${TAG} created"