import { Button } from '@/components/ui/button';
import { IntroPageProps } from '@/types/page';
import { Link, usePage } from '@inertiajs/react';
import {
   ArrowRight,
   Award,
   BarChart3,
   Check,
   GraduationCap,
   PlayCircle,
   Quote,
   ShieldCheck,
   Smartphone,
   Sparkles,
   Star,
   Users,
   Video,
   Zap,
} from 'lucide-react';

/**
 * Bespoke, self-contained marketing landing page in the style of modern SaaS
 * LMS sites (talentlms.com). Intentionally does NOT depend on dynamic course
 * data so it renders identically regardless of catalogue state.
 */
const TalentLanding = () => {
   const { props } = usePage<IntroPageProps>();
   const brand = props.system?.fields?.name || 'Carter LMS';

   const registerHref = (() => {
      try {
         return route('register');
      } catch {
         return '/register';
      }
   })();
   const loginHref = (() => {
      try {
         return route('login');
      } catch {
         return '/login';
      }
   })();

   const features = [
      { icon: Zap, title: 'Build courses in minutes', desc: 'A drag-and-drop builder with lessons, quizzes and files — no technical skills required.' },
      { icon: Sparkles, title: 'AI-assisted authoring', desc: 'Generate lessons, summaries and assessments in seconds and ship training faster.' },
      { icon: Video, title: 'Live & video classes', desc: 'Run live sessions and AI video tutors with built-in Zoom and Tavus integrations.' },
      { icon: Award, title: 'Certificates & exams', desc: 'Award branded certificates and validate knowledge with timed, graded exams.' },
      { icon: BarChart3, title: 'Reports & analytics', desc: 'Track completion, engagement and results with dashboards that update in real time.' },
      { icon: Smartphone, title: 'Learn on any device', desc: 'A fast, responsive experience so your people can learn at their desk or on the go.' },
   ];

   const stats = [
      { value: '70,000+', label: 'Teams trained' },
      { value: '5M+', label: 'Learners onboarded' },
      { value: '92%', label: 'Course completion' },
      { value: '120+', label: 'Countries served' },
   ];

   const rows = [
      {
         eyebrow: 'Course building',
         title: 'Launch beautiful courses, fast',
         desc: 'Assemble video, text, quizzes and downloads into polished courses with a builder anyone on your team can use. Reuse content across programs and publish in a click.',
         points: ['Drag-and-drop lesson builder', 'Reusable content library', 'One-click publishing'],
         accent: 'from-violet-500/15 to-indigo-500/10',
      },
      {
         eyebrow: 'Engagement',
         title: 'Teach live — or let AI do it',
         desc: 'Host live classes with Zoom or deploy an always-on AI video tutor with Tavus. Keep learners engaged with discussions, streaks and gamified progress.',
         points: ['Native Zoom live classes', 'AI video tutor (Tavus)', 'Gamification & progress tracking'],
         accent: 'from-emerald-500/15 to-teal-500/10',
      },
      {
         eyebrow: 'Insights',
         title: 'Measure what actually matters',
         desc: 'See who completed what, where learners drop off, and how training maps to results — all in dashboards built for admins and instructors alike.',
         points: ['Real-time completion reports', 'Per-learner activity timelines', 'Exportable analytics'],
         accent: 'from-sky-500/15 to-blue-500/10',
      },
   ];

   const testimonials = [
      { quote: `${brand} cut our onboarding time in half. New hires are productive in days, not weeks.`, name: 'Sarah Mitchell', role: 'Head of People, Northwind' },
      { quote: 'The reporting alone paid for itself. We finally know our training drives results.', name: 'David Okafor', role: 'L&D Lead, Brightpath' },
      { quote: 'Setup took an afternoon. The AI tutor feels like having an instructor on call 24/7.', name: 'Lena Fischer', role: 'Operations, Vela Group' },
   ];

   return (
      <div className="bg-background text-foreground">
         {/* ================= HERO ================= */}
         <section className="relative overflow-hidden">
            <div className="pointer-events-none absolute -top-32 -right-24 h-[420px] w-[420px] rounded-full bg-primary/20 blur-[160px]" />
            <div className="pointer-events-none absolute -bottom-32 -left-24 h-[420px] w-[420px] rounded-full bg-emerald-500/20 blur-[160px]" />

            <div className="mx-auto grid max-w-7xl items-center gap-12 px-4 pt-16 pb-12 md:grid-cols-2 md:pt-24 md:pb-20">
               <div className="relative z-10">
                  <span className="inline-flex items-center gap-2 rounded-full border border-border bg-muted/50 px-4 py-1.5 text-sm font-medium text-muted-foreground">
                     <Sparkles className="h-4 w-4 text-primary" />
                     The training platform teams rank #1
                  </span>

                  <h1 className="mt-6 text-4xl leading-[1.1] font-extrabold tracking-tight md:text-5xl lg:text-6xl">
                     Train your people.{' '}
                     <span className="bg-gradient-to-r from-primary to-emerald-500 bg-clip-text text-transparent">Measure results.</span> Grow.
                  </h1>

                  <p className="mt-5 max-w-xl text-lg text-muted-foreground">
                     {brand} is the all-in-one learning platform to build courses, train live or with AI, and prove the impact — without the busywork.
                  </p>

                  <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                     <Button asChild size="lg" className="h-12 px-7 text-base">
                        <Link href={registerHref}>
                           Get started free <ArrowRight className="h-4 w-4" />
                        </Link>
                     </Button>
                     <Button asChild size="lg" variant="outline" className="h-12 px-7 text-base">
                        <a href="#features">
                           <PlayCircle className="h-4 w-4" /> See how it works
                        </a>
                     </Button>
                  </div>

                  <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-muted-foreground">
                     <div className="flex items-center gap-1.5">
                        {[0, 1, 2, 3, 4].map((i) => (
                           <Star key={i} className="h-4 w-4 fill-amber-400 text-amber-400" />
                        ))}
                        <span className="ml-1 font-medium text-foreground">4.8/5</span> from 2,000+ reviews
                     </div>
                     <span className="hidden h-4 w-px bg-border sm:block" />
                     <span className="inline-flex items-center gap-1.5">
                        <ShieldCheck className="h-4 w-4 text-emerald-500" /> No credit card required
                     </span>
                  </div>
               </div>

               {/* Mock product panel */}
               <div className="relative z-10">
                  <div className="rounded-2xl border border-border bg-card p-3 shadow-2xl shadow-primary/10">
                     <div className="flex items-center gap-1.5 px-2 pb-3">
                        <span className="h-3 w-3 rounded-full bg-red-400" />
                        <span className="h-3 w-3 rounded-full bg-amber-400" />
                        <span className="h-3 w-3 rounded-full bg-emerald-400" />
                     </div>
                     <div className="rounded-xl bg-gradient-to-br from-muted/60 to-muted/20 p-5">
                        <div className="flex items-center justify-between">
                           <div>
                              <p className="text-sm text-muted-foreground">Welcome back 👋</p>
                              <p className="text-lg font-semibold">Your learning dashboard</p>
                           </div>
                           <span className="rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">92% complete</span>
                        </div>

                        <div className="mt-5 space-y-3">
                           {[
                              { name: 'Onboarding Essentials', pct: 100, color: 'bg-emerald-500' },
                              { name: 'Data Security 101', pct: 76, color: 'bg-primary' },
                              { name: 'Leadership Track', pct: 45, color: 'bg-sky-500' },
                           ].map((c) => (
                              <div key={c.name} className="rounded-lg border border-border bg-card p-3">
                                 <div className="mb-2 flex items-center justify-between text-sm">
                                    <span className="font-medium">{c.name}</span>
                                    <span className="text-muted-foreground">{c.pct}%</span>
                                 </div>
                                 <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                    <div className={`h-full rounded-full ${c.color}`} style={{ width: `${c.pct}%` }} />
                                 </div>
                              </div>
                           ))}
                        </div>

                        <div className="mt-5 grid grid-cols-3 gap-3">
                           {[
                              { icon: Users, label: 'Learners', value: '1,284' },
                              { icon: Award, label: 'Certificates', value: '317' },
                              { icon: BarChart3, label: 'Avg. score', value: '88%' },
                           ].map((s) => (
                              <div key={s.label} className="rounded-lg border border-border bg-card p-3 text-center">
                                 <s.icon className="mx-auto h-5 w-5 text-primary" />
                                 <p className="mt-1 text-sm font-semibold">{s.value}</p>
                                 <p className="text-xs text-muted-foreground">{s.label}</p>
                              </div>
                           ))}
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>

         {/* ================= LOGOS ================= */}
         <section className="border-y border-border bg-muted/30">
            <div className="mx-auto max-w-7xl px-4 py-10">
               <p className="text-center text-sm font-medium tracking-wide text-muted-foreground uppercase">Trusted by fast-growing teams worldwide</p>
               <div className="mt-6 flex flex-wrap items-center justify-center gap-x-12 gap-y-4 opacity-70">
                  {['Northwind', 'Brightpath', 'Vela Group', 'Acme Co', 'Lumen', 'Quanta'].map((name) => (
                     <span key={name} className="text-lg font-bold tracking-tight text-muted-foreground">
                        {name}
                     </span>
                  ))}
               </div>
            </div>
         </section>

         {/* ================= STATS ================= */}
         <section className="mx-auto max-w-7xl px-4 py-14">
            <div className="grid grid-cols-2 gap-6 md:grid-cols-4">
               {stats.map((s) => (
                  <div key={s.label} className="text-center">
                     <p className="text-3xl font-extrabold md:text-4xl">{s.value}</p>
                     <p className="mt-1 text-sm text-muted-foreground">{s.label}</p>
                  </div>
               ))}
            </div>
         </section>

         {/* ================= FEATURES ================= */}
         <section id="features" className="mx-auto max-w-7xl px-4 py-16">
            <div className="mx-auto max-w-2xl text-center">
               <span className="text-sm font-semibold tracking-wide text-primary uppercase">Everything you need</span>
               <h2 className="mt-3 text-3xl font-bold tracking-tight md:text-4xl">One platform for all your training</h2>
               <p className="mt-4 text-lg text-muted-foreground">From the first lesson to the final certificate — build, deliver and measure learning in one place.</p>
            </div>

            <div className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
               {features.map((f) => (
                  <div key={f.title} className="group rounded-2xl border border-border bg-card p-6 transition-shadow hover:shadow-lg">
                     <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                        <f.icon className="h-6 w-6" />
                     </div>
                     <h3 className="mt-5 text-lg font-semibold">{f.title}</h3>
                     <p className="mt-2 text-muted-foreground">{f.desc}</p>
                  </div>
               ))}
            </div>
         </section>

         {/* ================= ALTERNATING ROWS ================= */}
         <section className="mx-auto max-w-7xl space-y-20 px-4 py-16">
            {rows.map((row, i) => (
               <div key={row.title} className="grid items-center gap-10 md:grid-cols-2">
                  <div className={i % 2 === 1 ? 'md:order-2' : ''}>
                     <span className="text-sm font-semibold tracking-wide text-primary uppercase">{row.eyebrow}</span>
                     <h3 className="mt-3 text-2xl font-bold tracking-tight md:text-3xl">{row.title}</h3>
                     <p className="mt-4 text-lg text-muted-foreground">{row.desc}</p>
                     <ul className="mt-6 space-y-3">
                        {row.points.map((p) => (
                           <li key={p} className="flex items-center gap-3">
                              <span className="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-600">
                                 <Check className="h-4 w-4" />
                              </span>
                              <span className="font-medium">{p}</span>
                           </li>
                        ))}
                     </ul>
                  </div>
                  <div className={i % 2 === 1 ? 'md:order-1' : ''}>
                     <div className={`relative rounded-2xl border border-border bg-gradient-to-br ${row.accent} p-8`}>
                        <div className="rounded-xl border border-border bg-card p-6 shadow-xl">
                           <div className="flex items-center gap-3">
                              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                 <GraduationCap className="h-5 w-5" />
                              </div>
                              <div className="flex-1">
                                 <div className="h-2.5 w-2/3 rounded-full bg-muted" />
                                 <div className="mt-2 h-2 w-1/3 rounded-full bg-muted/70" />
                              </div>
                           </div>
                           <div className="mt-5 space-y-3">
                              <div className="h-2.5 w-full rounded-full bg-muted" />
                              <div className="h-2.5 w-5/6 rounded-full bg-muted" />
                              <div className="h-2.5 w-4/6 rounded-full bg-muted" />
                           </div>
                           <div className="mt-6 flex gap-3">
                              <div className="h-9 flex-1 rounded-lg bg-primary/15" />
                              <div className="h-9 w-24 rounded-lg bg-emerald-500/15" />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            ))}
         </section>

         {/* ================= TESTIMONIALS ================= */}
         <section className="border-y border-border bg-muted/30">
            <div className="mx-auto max-w-7xl px-4 py-16">
               <div className="mx-auto max-w-2xl text-center">
                  <h2 className="text-3xl font-bold tracking-tight md:text-4xl">Loved by teams that take training seriously</h2>
               </div>
               <div className="mt-12 grid gap-6 md:grid-cols-3">
                  {testimonials.map((t) => (
                     <figure key={t.name} className="flex flex-col rounded-2xl border border-border bg-card p-6">
                        <Quote className="h-7 w-7 text-primary/30" />
                        <blockquote className="mt-3 flex-1 text-foreground">“{t.quote}”</blockquote>
                        <figcaption className="mt-6 flex items-center gap-3">
                           <span className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary">
                              {t.name.split(' ').map((n) => n[0]).join('')}
                           </span>
                           <span>
                              <p className="text-sm font-semibold">{t.name}</p>
                              <p className="text-xs text-muted-foreground">{t.role}</p>
                           </span>
                        </figcaption>
                     </figure>
                  ))}
               </div>
            </div>
         </section>

         {/* ================= FINAL CTA ================= */}
         <section className="mx-auto max-w-7xl px-4 py-20">
            <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-indigo-600 px-6 py-16 text-center text-primary-foreground">
               <div className="pointer-events-none absolute -top-20 -right-10 h-64 w-64 rounded-full bg-white/10 blur-3xl" />
               <div className="pointer-events-none absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-emerald-300/20 blur-3xl" />
               <h2 className="relative text-3xl font-bold tracking-tight md:text-4xl">Ready to build training your team will love?</h2>
               <p className="relative mx-auto mt-4 max-w-xl text-lg text-primary-foreground/80">
                  Spin up your academy in minutes. Free to start — upgrade when you’re ready.
               </p>
               <div className="relative mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                  <Button asChild size="lg" variant="secondary" className="h-12 px-7 text-base">
                     <Link href={registerHref}>
                        Get started free <ArrowRight className="h-4 w-4" />
                     </Link>
                  </Button>
                  <Button asChild size="lg" variant="outline" className="h-12 border-white/30 bg-transparent px-7 text-base text-primary-foreground hover:bg-white/10">
                     <Link href={loginHref}>Sign in</Link>
                  </Button>
               </div>
            </div>
         </section>
      </div>
   );
};

export default TalentLanding;
