/**
 * Created by egorov on 07.10.2015.
 * Asynchronous file uploader
 */
var asyncFileUploader = function(file, options) {

    // Check params
    if (options === undefined || options.url === undefined) {
        console.log('Cannot upload file asynchronously. You should pass {url:"..."} as a second parameter');
        return true;
    }

    if(options.responseType === undefined) {
        options.responseType = 'json';
    }

    // Define generic handlers to avoid further ifs
    if (!options.progressHandler) {
        options.progressHandler = function (e) {
        };
    }
    if (!options.errorHandler) {
        options.errorHandler = function (e) {
            console.log('Cannot upload file asynchronously. Error: '+ e.toString());
        };
    }
    if (!options.startHandler) {
        options.startHandler = function (file) {
            return true;
        };
    }
    if (!options.finishHandler) {
        options.finishHandler = function (e) {
        };
    }
    if (!options.successHandler) {
        options.successHandler = function (response, status, xhr) {
        };
    }
    if (!options.endHandler) {
        options.endHandler = function (xhr) {
        };
    }

    // Create async upload request
    var xhr = new XMLHttpRequest();

    // Upload progress handler
    xhr.upload.addEventListener("progress", options.progressHandler, false);

    // Upload error handler
    xhr.upload.addEventListener("error", options.errorHandler, false);

    // Upload success handler
    xhr.upload.addEventListener("load", options.finishHandler, false);

    // Before request handler
    if (options.startHandler(file)) {
        // Perform request
        xhr.open("POST", options.url, true);
        xhr.setRequestHeader("Cache-Control", "no-cache");
        xhr.setRequestHeader("Content-Type", "multipart/form-data");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(file.name));
        xhr.setRequestHeader("X-File-Size", file.size);
        xhr.setRequestHeader("X-File-Type", file.type);

        // Add special http request header for SamsonPHP backend
        xhr.setRequestHeader('SJSAsync', 'true');

        // Send file
        xhr.send(file);

        // Response handler
        xhr.onreadystatechange = function () {
            // Final async stage
            if (xhr.readyState == 4) {
                var response = xhr.responseText.trim();

                // If we are waiting for json reponse
                if (options.responseType === 'json') {
                    try { // Lets try to decode it
                        response = JSON.parse(response);
                    } catch (e) {
                        console.log('Error receiving reponse after asynchronous file upload. Error: '+ e.toString());
                    }
                }

                options.successHandler(response, xhr.status, xhr);
            }

            options.endHandler(xhr);
        };
    }
};

