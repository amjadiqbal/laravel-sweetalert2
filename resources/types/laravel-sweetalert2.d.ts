/**
 * Types for the browser API of amjadiqbal/laravel-sweetalert2 (window.LaravelSwal and Alpine's $swal).
 * Reference it with: /// <reference path="../../vendor/amjadiqbal/laravel-sweetalert2/resources/types/laravel-sweetalert2.d.ts" />
 *
 * Messages are text, not HTML: a string's first line is the title, the rest is the text, and line
 * breaks are kept. Only an explicit `html` option is inserted as HTML; never put user input in it.
 */

/** Any SweetAlert2 option (https://sweetalert2.github.io/#configuration), e.g. icon, confirmButtonText, input. */
export interface SwalOptions {
    title?: string;
    titleText?: string;
    text?: string;
    /** Trusted HTML only. */
    html?: string;
    icon?: 'success' | 'error' | 'warning' | 'info' | 'question';
    confirmButtonText?: string;
    cancelButtonText?: string;
    denyButtonText?: string;
    footer?: string;
    input?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'textarea' | 'select' | 'radio' | 'checkbox' | 'range' | 'date' | 'time';
    inputValue?: string;
    inputPlaceholder?: string;
    inputLabel?: string;
    toast?: boolean;
    timer?: number;
    position?: 'top' | 'top-start' | 'top-end' | 'center' | 'center-start' | 'center-end' | 'bottom' | 'bottom-start' | 'bottom-end';
    theme?: 'light' | 'dark' | 'auto' | string;
    [option: string]: unknown;
}

export type SwalMessage = string | SwalOptions;

export interface LaravelSwalDialogs {
    /** Resolves true when the user confirms, false when they cancel or dismiss. */
    confirm(message?: SwalMessage, options?: SwalOptions): Promise<boolean>;
    /** Resolves the entered text, or null when cancelled (like window.prompt). */
    prompt(message?: SwalMessage, defaultValueOrOptions?: string | SwalOptions): Promise<string | null>;
    /** Resolves once the dialog is closed. */
    alert(message?: SwalMessage, options?: SwalOptions): Promise<void>;
    /** Swal.fire() with the configured defaults; options are passed as given. */
    fire(options: SwalOptions): Promise<unknown>;
    /** Queue a toast. */
    toast(options: SwalOptions): Promise<unknown>;
}

export interface LaravelSwalApi extends LaravelSwalDialogs {
    configure(settings: {
        defaults?: SwalOptions;
        toast?: SwalOptions;
        confirm?: SwalOptions;
        prompt?: SwalOptions;
        livewireConfirm?: boolean;
    }): LaravelSwalApi;
    queue(list: SwalOptions[]): Promise<unknown>;
}

declare global {
    interface Window {
        LaravelSwal: LaravelSwalApi;
        LaravelSweetAlert2: LaravelSwalApi;
    }
}
