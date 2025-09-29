export function useColorScheme() {
    const getColorScheme = (bgColor) => {
        const colorMap = {
            'bg-blue-50': { text: 'text-blue-700', hover: 'text-blue-700', hoverBg: 'hover:bg-blue-100' },
            'bg-orange-100': { text: 'text-orange-700', hover: 'text-orange-700', hoverBg: 'hover:bg-orange-200' },
            'bg-pink-50': { text: 'text-pink-700', hover: 'text-pink-700', hoverBg: 'hover:bg-pink-100' },
            'bg-emerald-50': { text: 'text-emerald-700', hover: 'text-emerald-700', hoverBg: 'hover:bg-emerald-100' },
            'bg-gray-100': { text: 'text-gray-700', hover: 'text-gray-700', hoverBg: 'hover:bg-gray-200' },
            'bg-purple-100': { text: 'text-purple-700', hover: 'text-purple-700', hoverBg: 'hover:bg-purple-200' },
            'bg-cyan-100': { text: 'text-cyan-700', hover: 'text-cyan-700', hoverBg: 'hover:bg-teal-200' },
            'bg-yellow-100': { text: 'text-yellow-700', hover: 'text-yellow-700', hoverBg: 'hover:bg-yellow-200' },
        }
        return colorMap[bgColor] || { text: 'text-gray-700', hover: 'text-gray-700', hoverBg: 'hover:bg-gray-200' }
    }

    return {
        getColorScheme
    }
}