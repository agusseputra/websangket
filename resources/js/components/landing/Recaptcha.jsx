import { useEffect, useImperativeHandle, useRef } from 'react';

const URL_SKRIP = 'https://www.google.com/recaptcha/api.js?render=explicit&hl=id';

export const SITE_KEY = document.querySelector('meta[name="recaptcha-site-key"]')?.content ?? '';

function muatSkrip() {
    if (!document.querySelector(`script[src="${URL_SKRIP}"]`)) {
        const skrip = document.createElement('script');
        skrip.src = URL_SKRIP;
        skrip.async = true;
        document.head.append(skrip);
    }
}

export default function Recaptcha({ onUbah, ref }) {
    const wadahRef = useRef(null);
    const widgetRef = useRef(null);
    const onUbahRef = useRef(onUbah);

    useEffect(() => {
        onUbahRef.current = onUbah;
    }, [onUbah]);

    useImperativeHandle(ref, () => ({
        reset() {
            if (widgetRef.current !== null) window.grecaptcha?.reset(widgetRef.current);
            onUbahRef.current('');
        },
    }), []);

    useEffect(() => {
        if (!SITE_KEY) return undefined;

        muatSkrip();

        const pasang = setInterval(() => {
            if (!window.grecaptcha?.render || widgetRef.current !== null) return;

            clearInterval(pasang);

            const tema = document.documentElement.dataset.theme
                ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

            widgetRef.current = window.grecaptcha.render(wadahRef.current, {
                sitekey: SITE_KEY,
                theme: tema,
                callback: (token) => onUbahRef.current(token),
                'expired-callback': () => onUbahRef.current(''),
                'error-callback': () => onUbahRef.current(''),
            });
        }, 200);

        return () => clearInterval(pasang);
    }, []);

    if (!SITE_KEY) return null;

    return <div ref={wadahRef} className="min-h-[78px] max-w-full overflow-x-auto" />;
}
