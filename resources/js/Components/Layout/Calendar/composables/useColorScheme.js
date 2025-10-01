export function useColorScheme() {
    const getColorScheme = (bgColor) => {
        const colorMap = {
            'bg-blue-50': { text: 'text-blue-700', hover: 'text-blue-700', hoverBg: 'hover:bg-blue-100' },
            'bg-amber-100': { text: 'text-amber-700', hover: 'text-amber-700', hoverBg: 'hover:bg-amber-200' },
            'bg-pink-50': { text: 'text-pink-700', hover: 'text-pink-700', hoverBg: 'hover:bg-pink-100' },
            'bg-green-50': { text: 'text-green-700', hover: 'text-green-700', hoverBg: 'hover:bg-green-100' },
            'bg-gray-100': { text: 'text-gray-700', hover: 'text-gray-700', hoverBg: 'hover:bg-gray-200' },
            'bg-purple-100': { text: 'text-purple-700', hover: 'text-purple-700', hoverBg: 'hover:bg-purple-200' },
            'bg-cyan-100': { text: 'text-cyan-700', hover: 'text-cyan-700', hoverBg: 'hover:bg-teal-200' },
            'bg-yellow-100': { text: 'text-yellow-700', hover: 'text-yellow-700', hoverBg: 'hover:bg-yellow-200' },
            'bg-lime-100': { text: 'text-lime-700', hover: 'text-lime-700', hoverBg: 'hover:bg-lime-200' },
            'bg-red-100': { text: 'text-red-700', hover: 'text-red-700', hoverBg: 'hover:bg-red-200' },
        }
        return colorMap[bgColor] || { text: 'text-gray-700', hover: 'text-gray-700', hoverBg: 'hover:bg-gray-200' }
    }

    return {
        getColorScheme
    }
}