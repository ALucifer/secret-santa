// src/utils/routing.ts
type Params = Record<string, any>;
interface RoutingType {
    generate: (name: string, params?: Params, absolute?: boolean) => string;
}

declare global {
    interface Window { Routing: RoutingType | undefined }
}

if (!window.Routing) {
    throw new Error(
        "[FOSJsRouting] window.Routing est indisponible. " +
        "Vérifie l’ordre et le type des <script> dans ton layout (pas type='module')."
    );
}

const Routing = window.Routing!;
export default Routing;
