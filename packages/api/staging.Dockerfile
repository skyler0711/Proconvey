# Base on the official Vapor PHP 8.2 image
FROM laravelphp/vapor:php82

# Copy in the application
COPY . /var/task

# PDFtk requires Java, we can use headless since there's no GUI
RUN apk add openjdk11-jre-headless

# Copy in the PDFtk binaries
COPY --from=pdftk/pdftk /usr/share/java /usr/share/java
COPY --from=pdftk/pdftk /usr/bin/pdftk /usr/bin/pdftk
