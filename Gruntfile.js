module.exports = function (grunt) {
    grunt.initConfig({
        shell:{
            doxygen:{
                command: 'doxygen documentation\\docConfig'
            }
        }
    });
    grunt.loadNpmTasks('grunt-shell');
    grunt.registerTask('default',['shell']);
};
