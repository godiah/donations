import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans], // Primary font for body text
                heading: ["Poppins", ...defaultTheme.fontFamily.sans], // Modern, friendly font for headings
                serif: ["Lora", ...defaultTheme.fontFamily.serif], // Optional serif for quotes or hero sections
            },
            colors: {
                primary: {
                    DEFAULT: "#3B82F6", // Primary blue for buttons, links, active states
                    100: "#DBEAFE",
                    200: "#BFDBFE",
                    300: "#93C5FD",
                    400: "#60A5FA",
                    500: "#3B82F6",
                    600: "#2563EB",
                    700: "#1D4ED8",
                },
                secondary: {
                    DEFAULT: "#F59E0B", // Warm orange for secondary buttons, accents, hover effects
                    100: "#FEF3C7",
                    200: "#FDE68A",
                    300: "#FCD34D",
                    400: "#FBBF24",
                    500: "#F59E0B",
                    600: "#D97706",
                    700: "#B45309",
                },
                success: {
                    DEFAULT: "#10B981", // Green for success messages, progress indicators, valid inputs
                    100: "#D1FAE5",
                    200: "#A7F3D0",
                    300: "#6EE7B7",
                    400: "#34D399",
                    500: "#10B981",
                    600: "#059669",
                    700: "#047857",
                },
                danger: {
                    DEFAULT: "#EF4444", // Red for error messages, warning icons, invalid inputs
                    100: "#FEE2E2",
                    200: "#FECACA",
                    300: "#FCA5A5",
                    400: "#F87171",
                    500: "#EF4444",
                    600: "#DC2626",
                    700: "#B91C1C",
                },
                neutral: {
                    50: "#F9FAFB", // Off-white for page/card/modal backgrounds
                    100: "#F3F4F6", // Light gray for section dividers, table rows, disabled states
                    200: "#E5E7EB",
                    300: "#D1D5DB",
                    500: "#6B7280", // Medium gray for secondary text, borders, icons
                    700: "#374151",
                    800: "#1F2A44", // Dark gray for primary text, navigation, form labels
                },
            },
        },
    },

    plugins: [forms],
};
