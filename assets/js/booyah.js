/**
 * Booyah!
 *
 * The easy mode template parser.
 *
 * User: Kevin Dees
 * Date: 9/6/13
 * Time: 10:37 AM
 */

var booyah = {

  templateTagKeys: [],
  templateTagValues: [],
  templateArray: [],
  templateString: '',

  ready: function() {
    this.templateString = this.templateArray.join('');
    this.replaceTags( this.templateString );

    this.templateTagKeys = [];
    this.templateTagValues = [];
    this.templateArray = [];

    return this.templateString;
  },

  addTag: function(key, value) {
    this.templateTagKeys.push(key);
    this.templateTagValues.push(value);

    return this;
  },

  addTemplate: function(string) {
    this.templateArray.push(string);

    return this;
  },

  replaceTags: function(string) {
    var tagCount = this.templateTagKeys.length;

    for(var i = 0; tagCount > i; i++) {
      var replaceTag = this.templateTagKeys[i];
      var withThisValue = this.templateTagValues[i];
      this.templateString = this.templateString.replace(new RegExp(replaceTag), withThisValue);
    }
  }

}
