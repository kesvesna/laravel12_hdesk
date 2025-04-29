const uniqId = (() => {
    let i = 25;
    return () => {
        return i++;
    }
})();
