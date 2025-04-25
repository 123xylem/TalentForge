export function useTextFormatter() {
    const truncate = (text: string, length: number = 100, suffix: string = '...') => {
        if (!text) return '';
        if (text.length > length) {
            return text.substring(0, length) + suffix;
        }
        return text;
    };

    return {
        truncate,
    };
}
