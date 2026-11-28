import {
    createMbtiObsidianGraph,
    normalizeAspectList,
    toggleAspectInArray,
} from './mbti-obsidian-graph';

window.mbtiGraphRuntime = {
    create: createMbtiObsidianGraph,
    normalizeAspects: normalizeAspectList,
    toggleAspect: toggleAspectInArray,
};
