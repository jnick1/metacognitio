/**
 * Created by Jacob on 4/4/2017.
 */
module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        // uglify: {
        //     options: {
        //         banner: "/*! <%= pkg.name %> <%= grunt.template.today('yyyy-mm-dd') %> */\n"
        //     },
        //     build: {
        //         src: "resources/src/<%= pkg.name %>.js",
        //         dest: "resources/build/<%= pkg.name %>.min.js"
        //     }
        // },
        jshint: {
            all: [
                "Gruntfile.js",
                "resources/src/*.js"
            ]
        },
        watch: {
            scripts: {
                files: [
                    "Gruntfile.js",
                    "resources/src/*.js"
                ],
                tasks: ["uglify"],
                options: {
                    interrupt: true
                }
            }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks("grunt-contrib-watch");

    // Default task(s).
    grunt.registerTask("default", ["uglify"]);
};