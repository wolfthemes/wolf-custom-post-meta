module.exports = {

	build: {

		options : {
			banner : '/*! <%= app.name %> Wordpress Plugin v<%= app.version %> */ \n',
			preserveComments : 'some'
		},

		files: {
			'<%= app.jsPath %>/wcpm.min.js': [
				'<%= app.jsPath %>/wcpm.js'
			],
		}
	}
};