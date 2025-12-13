import '@tavus/cvi-ui/dist/style.css';
import { useEffect, useState } from 'react';

// Temporarily mock until Tavus components are properly installed
const DailyVideo = ({ conversationId, replicaId, apiKey, className }: any) => {
   return (
      <div className={className}>
         <div className="flex h-full items-center justify-center bg-muted">
            <div className="text-center p-8">
               <h3 className="text-lg font-semibold mb-2">Tavus Interactive Video</h3>
               <p className="text-sm text-muted-foreground mb-4">
                  Tavus components need to be installed to display interactive AI video.
               </p>
               <div className="text-xs space-y-1 text-left max-w-md mx-auto bg-background p-4 rounded border">
                  <p><strong>Conversation ID:</strong> {conversationId || 'N/A'}</p>
                  <p><strong>Replica ID:</strong> {replicaId || 'N/A'}</p>
                  <p><strong>API Key:</strong> {apiKey ? '••••••••' : 'N/A'}</p>
               </div>
            </div>
         </div>
      </div>
   );
};

interface TavusPlayerProps {
   conversationId?: string;
   replicaId?: string;
   apiKey?: string;
   translate?: any;
}

const TavusPlayer = ({ conversationId, replicaId, apiKey, translate }: TavusPlayerProps) => {
   const [error, setError] = useState<string | null>(null);

   useEffect(() => {
      if (!conversationId && !replicaId) {
         setError('Either conversationId or replicaId is required');
      }
      if (!apiKey) {
         setError('API key is required');
      }
   }, [conversationId, replicaId, apiKey]);

   if (error) {
      return (
         <div className="flex h-full items-center justify-center">
            <p className="text-destructive">{error}</p>
         </div>
      );
   }

   return (
      <div className="relative h-full w-full">
         <DailyVideo
            conversationId={conversationId}
            replicaId={replicaId}
            apiKey={apiKey}
            className="h-full w-full"
         />
      </div>
   );
};

export default TavusPlayer;
