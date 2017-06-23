module.exports = function(html, options) {
    var re = /{{(.+?)}}/g, 
        cursor = 0, 
        r = [],
        result;
    while(match = re.exec(html)) {
        r.push(html.slice(cursor, match.index));
        r.push(options[match[1]]);
        cursor = match.index + match[0].length;
    }
    r.push(html.substr(cursor, html.length - cursor));
    result = r.join('').replace(/[\r\t\n]/g, ' ');
    //code = (code + 'return r.join(""); }').replace(/[\r\t\n]/g, ' ');
    //try { result = new Function('obj', code).apply(options, [options]); }
    //catch(err) { console.error("'" + err.message + "'", " in \n\nCode:\n", code, "\n"); }
    return result;
};
