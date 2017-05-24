module.exports = {
	
	minify: {
		expand: true,
		cwd: '<%= app.cssPath %>',
		src: ['*.css', '!*.min.css'],
		dest: '<%= app.cssPath %>',
		ext: '.min.css'
	}
};