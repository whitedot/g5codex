const {
  fs,
  file,
  path,
  phpFiles,
  listFiles,
  assertContains,
  assertFileMissing,
  assertNoMatches,
} = require('./lib/refactor-check-utils');

const currentPlan = file('docs/gnuboard5-readable-refactor-plan.md');
const maintainerGuide = file('docs/legacy-maintainer-guide.md');
const docFiles = [
  file('README.md'),
  ...listFiles(file('docs'), {
    recursive: true,
    filter: filePath => /\.(md|txt)$/.test(filePath),
  }),
];

assertContains(
  currentPlan,
  /^# Gnuboard5 Readable Refactor Plan/m,
  'current readable refactor plan is missing or has an unexpected title'
);

assertContains(
  currentPlan,
  /tag_html[\s\S]*(?:link|meta|script)|(?:link|meta|script)[\s\S]*tag_html/,
  'current plan should document shell tag_html view-model usage'
);

assertContains(
  maintainerGuide,
  /tag_html.*속성 escape/s,
  'maintainer guide should explain tag_html escape responsibility'
);

[
  'docs/current-work-plan-2026-04-26.md',
  'docs/architecture/member-collaboration-phase1.md',
  'docs/project-evaluation-report-2026-04-22.md',
].forEach(oldPlan => {
  assertFileMissing(file(oldPlan), `legacy plan document should not exist: ${oldPlan}`);
});

assertNoMatches(
  'legacy plan document references',
  docFiles,
  /current-work-plan-2026-04-26|member-collaboration-phase1|project-evaluation-report-2026-04-22/
);

assertNoMatches(
  'legacy plan document references in PHP comments',
  [
    ...phpFiles(file('adm'), true),
    ...phpFiles(file('member'), true),
    ...phpFiles(file('lib/domain'), true),
  ],
  /current-work-plan-2026-04-26|member-collaboration-phase1|project-evaluation-report-2026-04-22/
);

assertNoMatches(
  'nonexistent legacy member render file references',
  docFiles,
  /render-auth\.lib\.php/
);

const checkScripts = listFiles(file('scripts'), {
  recursive: false,
  filter: filePath => /^check-.*\.js$/.test(path.basename(filePath)),
});

checkScripts.forEach(scriptPath => {
  const powershellPath = scriptPath.replace(/\.js$/, '.ps1');
  if (!fs.existsSync(powershellPath)) {
    console.error(`missing PowerShell wrapper for check script: ${powershellPath}`);
    process.exit(1);
  }
});

console.log('Plan document checks passed.');
