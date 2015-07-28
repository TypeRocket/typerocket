if (typeof window.trRepeaterCallback === 'object') {
} else {
    window.trRepeaterCallback = [];
}

if (typeof window.trHttpCallback === 'object') {
} else {
    window.trHttpCallback = [];
}

var TypeRocket = {
    httpCallbacks: [],
    repeaterCallbacks: []
};
