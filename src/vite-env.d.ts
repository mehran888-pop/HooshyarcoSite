/// <reference types="vite/client" />
/// <reference types="vite-plugin-pwa/client" />

interface ImportMetaEnv {
  readonly VITE_WP_URL?: string;
  readonly VITE_WP_URL_2?: string;
  readonly VITE_DEMO_MODE?: string;
  readonly VITE_WC_CONSUMER_KEY?: string;
  readonly VITE_WC_CONSUMER_SECRET?: string;
  readonly VITE_BALE_API?: string;
  readonly VITE_BALE_TOKEN?: string;
  readonly VITE_BALE_CHANNEL?: string;
  readonly VITE_GF_EMPLOYMENT?: string;
  readonly VITE_GF_CONSULTATION?: string;
  readonly VITE_GF_CONTACT?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
