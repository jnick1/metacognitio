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
        //         src: "java/src/<%= pkg.name %>.js",
        //         dest: "java/build/<%= pkg.name %>.min.js"
        //     }
        // },
        jshint: {
            all: [
                "Gruntfile.js",
                "java/src/*.js"
            ]
        },
        watch: {
            scripts: {
                files: [
                    "Gruntfile.js",
                    "java/src/*.js"
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